<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Project;
use App\Models\Reservation;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReservationService
{
    private function calculateReservationPercentage(?Unit $unit, float $amount): float
    {
        if (!$unit) {
            return 0.0;
        }

        $totalPrice = (float) $unit->total_price;
        if ($totalPrice <= 0) {
            return 0.0;
        }

        return round(($amount / $totalPrice) * 100, 2);
    }

    public function getReservationsPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Reservation::with(['client', 'project', 'unit', 'advisor'])
            ->orderBy('created_at', 'desc');

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('reservation_number', 'like', '%' . $search . '%')
                    ->orWhereHas('client', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('project', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (!empty($filters['advisor_id'])) {
            $query->byAdvisor($filters['advisor_id']);
        }

        if (!empty($filters['project_id'])) {
            $query->byProject($filters['project_id']);
        }

        if (!empty($filters['client_id'])) {
            $query->byClient($filters['client_id']);
        }

        if (!empty($filters['payment_status'])) {
            $query->byPaymentStatus($filters['payment_status']);
        }

        return $query->paginate($perPage);
    }

    public function getActiveProjects(): Collection
    {
        return Project::active()->get();
    }

    public function getActiveClients(): Collection
    {
        return Client::active()->get();
    }

    public function getAvailableUnitsForProject(int $projectId, ?int $includeUnitId = null): Collection
    {
        $query = Unit::where('project_id', $projectId)
            ->where('status', 'disponible');

        if ($includeUnitId) {
            $query->orWhere('id', $includeUnitId);
        }

        return $query->orderBy('unit_manzana')
            ->orderBy('unit_number')
            ->get();
    }

    public function createReservation(array $data, int $userId): Reservation
    {
        /** @var Unit|null $unit */
        $unit = Unit::find($data['unit_id']);
        if (!$unit) {
            throw new \InvalidArgumentException('La unidad seleccionada no existe.');
        }

        if ($unit->status !== 'disponible') {
            throw new \InvalidArgumentException('La unidad seleccionada no está disponible.');
        }

        $reservationDate = now();
        $expirationDate = $reservationDate->copy()->endOfDay();
        $reservationPercentage = $this->calculateReservationPercentage($unit, (float) $data['reservation_amount']);

        return DB::transaction(function () use ($data, $userId, $reservationPercentage, $reservationDate, $expirationDate) {
            return Reservation::create([
                'client_id' => $data['client_id'],
                'project_id' => $data['project_id'],
                'unit_id' => $data['unit_id'],
                'advisor_id' => $data['advisor_id'],
                'reservation_type' => 'pre_reserva',
                'status' => 'activa',
                'payment_status' => 'pendiente',
                'reservation_date' => $reservationDate,
                'expiration_date' => $expirationDate,
                'reservation_amount' => $data['reservation_amount'],
                'reservation_percentage' => $reservationPercentage,
                'payment_method' => $data['payment_method'] ?? null,
                'payment_reference' => $data['payment_reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'terms_conditions' => $data['terms_conditions'] ?? null,
                'image' => null,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);
        });
    }

    public function updateReservation(Reservation $reservation, array $data, int $userId): bool
    {
        /** @var Unit|null $unit */
        $unit = Unit::find($reservation->unit_id);
        $reservationPercentage = $unit
            ? $this->calculateReservationPercentage($unit, (float) $data['reservation_amount'])
            : 0.0;

        return DB::transaction(function () use ($reservation, $data, $userId, $reservationPercentage) {
            return $reservation->update([
                'client_id' => $data['client_id'],
                'project_id' => $reservation->project_id,
                'unit_id' => $reservation->unit_id,
                'advisor_id' => $data['advisor_id'],
                'reservation_type' => $data['reservation_type'],
                'status' => $reservation->status,
                'reservation_date' => $data['reservation_date'],
                'expiration_date' => $data['expiration_date'] ?? null,
                'reservation_amount' => $data['reservation_amount'],
                'reservation_percentage' => $reservationPercentage,
                'payment_method' => $data['payment_method'] ?? null,
                'payment_status' => $data['payment_status'],
                'payment_reference' => $data['payment_reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'terms_conditions' => $data['terms_conditions'] ?? null,
                'image' => $reservation->image,
                'updated_by' => $userId,
            ]);
        });
    }

    public function confirmReservationWithImage(Reservation $reservation, array $data, $image, int $userId): Reservation
    {
        $newImagePath = null;
        $reservationPercentage = 0.0;

        try {
            if ($image) {
                if ($reservation->image && Storage::disk('public')->exists($reservation->image)) {
                    Storage::disk('public')->delete($reservation->image);
                }
                $newImagePath = $image->store('reservations', 'public');
            }

            if ($reservation->unit) {
                $reservationPercentage = $this->calculateReservationPercentage(
                    $reservation->unit,
                    (float) $data['reservation_amount']
                );
            }

            return DB::transaction(function () use ($reservation, $data, $newImagePath, $userId, $reservationPercentage) {
                $reservation->update([
                    'reservation_date' => $data['reservation_date'],
                    'expiration_date' => $data['expiration_date'] ?? null,
                    'reservation_amount' => $data['reservation_amount'],
                    'reservation_percentage' => $reservationPercentage,
                    'payment_method' => $data['payment_method'] ?? null,
                    'payment_status' => $data['payment_status'],
                    'payment_reference' => $data['payment_reference'] ?? null,
                    'image' => $newImagePath ?? $reservation->image,
                    'status' => 'confirmada',
                    'updated_by' => $userId,
                ]);

                if ($reservation->unit) {
                    $unit = $reservation->unit;
                    if ($unit->status !== 'reservado') {
                        $unit->update(['status' => 'reservado']);
                        $unit->project->updateUnitCounts();
                    }
                }

                return $reservation;
            });
        } catch (\Exception $e) {
            if ($newImagePath && Storage::disk('public')->exists($newImagePath)) {
                Storage::disk('public')->delete($newImagePath);
            }
            throw $e;
        }
    }

    public function cancelReservation(Reservation $reservation, string $note, int $userId): Reservation
    {
        return DB::transaction(function () use ($reservation, $note, $userId) {
            $reservation->update([
                'status' => 'cancelada',
                'notes' => ($reservation->notes ?? '') . "\n\n[Cancelada] " . $note,
                'updated_by' => $userId,
            ]);

            if ($reservation->unit) {
                $unit = $reservation->unit;
                $unit->update(['status' => 'disponible']);
                $unit->project->updateUnitCounts();
            }

            return $reservation;
        });
    }
}
