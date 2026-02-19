<?php

namespace App\Http\Controllers\Api\Cazador;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Reservation;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExportController extends Controller
{
    use ApiResponse;

    public function clients(Request $request)
    {
        try {
            $filters = [
                'search' => $request->get('search'),
                'status' => $request->get('status'),
                'type' => $request->get('type'),
                'source' => $request->get('source'),
            ];

            $query = Client::query()
                ->where('assigned_advisor_id', Auth::id());

            if (!empty($filters['status'])) {
                $query->byStatus($filters['status']);
            }
            if (!empty($filters['type'])) {
                $query->byType($filters['type']);
            }
            if (!empty($filters['source'])) {
                $query->bySource($filters['source']);
            }
            if (!empty($filters['search'])) {
                $search = trim($filters['search']);
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('document_number', 'like', "%{$search}%");
                });
            }

            $filename = 'clientes_' . now()->format('Ymd_His') . '.csv';

            return response()->streamDownload(function () use ($query) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['id', 'name', 'phone', 'status', 'client_type', 'source', 'document_number']);
                $query->orderBy('created_at', 'desc')->chunk(200, function ($clients) use ($handle) {
                    foreach ($clients as $client) {
                        fputcsv($handle, [
                            $client->id,
                            $client->name,
                            $client->phone,
                            $client->status,
                            $client->client_type,
                            $client->source,
                            $client->document_number,
                        ]);
                    }
                });
                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv']);
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error al exportar clientes');
        }
    }

    public function reservations(Request $request)
    {
        try {
            $user = Auth::user();
            $filters = [
                'search' => $request->get('search'),
                'status' => $request->get('status'),
                'payment_status' => $request->get('payment_status'),
                'project_id' => $request->get('project_id'),
                'client_id' => $request->get('client_id'),
            ];

            $query = Reservation::query();
            if (!$user->isAdmin() && !$user->isLider()) {
                $query->where('advisor_id', $user->id);
            }

            if (!empty($filters['search'])) {
                $query->search($filters['search']);
            }
            if (!empty($filters['status'])) {
                $query->byStatus($filters['status']);
            }
            if (!empty($filters['payment_status'])) {
                $query->byPaymentStatus($filters['payment_status']);
            }
            if (!empty($filters['project_id'])) {
                $query->byProject($filters['project_id']);
            }
            if (!empty($filters['client_id'])) {
                $query->byClient($filters['client_id']);
            }

            $filename = 'reservas_' . now()->format('Ymd_His') . '.csv';

            return response()->streamDownload(function () use ($query) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, [
                    'id',
                    'reservation_number',
                    'client_id',
                    'project_id',
                    'unit_id',
                    'status',
                    'payment_status',
                    'reservation_date',
                    'expiration_date',
                    'reservation_amount',
                ]);
                $query->orderBy('created_at', 'desc')->chunk(200, function ($reservations) use ($handle) {
                    foreach ($reservations as $reservation) {
                        fputcsv($handle, [
                            $reservation->id,
                            $reservation->reservation_number,
                            $reservation->client_id,
                            $reservation->project_id,
                            $reservation->unit_id,
                            $reservation->status,
                            $reservation->payment_status,
                            optional($reservation->reservation_date)->format('Y-m-d'),
                            optional($reservation->expiration_date)->format('Y-m-d'),
                            $reservation->reservation_amount,
                        ]);
                    }
                });
                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv']);
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error al exportar reservas');
        }
    }

    public function salesReport(Request $request)
    {
        try {
            $from = $request->get('date_from');
            $to = $request->get('date_to');

            $query = Reservation::query()->byStatus('convertida_venta');
            if ($from) {
                $query->whereDate('reservation_date', '>=', $from);
            }
            if ($to) {
                $query->whereDate('reservation_date', '<=', $to);
            }

            $filename = 'reporte_ventas_' . now()->format('Ymd_His') . '.csv';

            return response()->streamDownload(function () use ($query) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, [
                    'id',
                    'reservation_number',
                    'client_id',
                    'project_id',
                    'unit_id',
                    'reservation_date',
                    'reservation_amount',
                ]);
                $query->orderBy('reservation_date', 'desc')->chunk(200, function ($reservations) use ($handle) {
                    foreach ($reservations as $reservation) {
                        fputcsv($handle, [
                            $reservation->id,
                            $reservation->reservation_number,
                            $reservation->client_id,
                            $reservation->project_id,
                            $reservation->unit_id,
                            optional($reservation->reservation_date)->format('Y-m-d'),
                            $reservation->reservation_amount,
                        ]);
                    }
                });
                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv']);
        } catch (\Exception $e) {
            return $this->serverErrorResponse($e, 'Error al exportar reporte de ventas');
        }
    }
}
