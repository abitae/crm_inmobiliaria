<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'project_id',
        'unit_id',
        'advisor_id',
        'reservation_number',
        'reservation_type', // pre_reserva, reserva_firmada, reserva_confirmada
        'status', // activa, confirmada, cancelada, vencida, convertida_venta
        'reservation_date',
        'expiration_date',
        'reservation_amount', // monto de la reserva
        'reservation_percentage', // porcentaje del precio total
        'payment_method',
        'payment_status', // pendiente, pagado, parcial
        'payment_reference',
        'notes',
        'terms_conditions',
        'client_signature',
        'advisor_signature',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'client_id' => 'integer',
        'project_id' => 'integer',
        'unit_id' => 'integer',
        'advisor_id' => 'integer',
        'reservation_amount' => 'decimal:2',
        'reservation_percentage' => 'decimal:2',
        'reservation_date' => 'datetime',
        'expiration_date' => 'datetime',
        'client_signature' => 'boolean',
        'advisor_signature' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relaciones
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function advisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'activa');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmada');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelada');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'vencida');
    }

    public function scopeConverted($query)
    {
        return $query->where('status', 'convertida_venta');
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeByUnit($query, $unitId)
    {
        return $query->where('unit_id', $unitId);
    }

    public function scopeByAdvisor($query, $advisorId)
    {
        return $query->where('advisor_id', $advisorId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('reservation_type', $type);
    }

    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->where('expiration_date', '>=', now())
            ->where('expiration_date', '<=', now()->addDays($days))
            ->where('status', 'activa');
    }

    public function scopeExpiredByDate($query)
    {
        return $query->where('expiration_date', '<', now())
            ->where('status', 'activa');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('reservation_date', [$startDate, $endDate]);
    }

    public function scopeByPaymentStatus($query, $paymentStatus)
    {
        return $query->where('payment_status', $paymentStatus);
    }

    // Accessors
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'activa';
    }

    public function getIsConfirmedAttribute(): bool
    {
        return $this->status === 'confirmada';
    }

    public function getIsCancelledAttribute(): bool
    {
        return $this->status === 'cancelada';
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiration_date && $this->expiration_date->isPast();
    }

    public function getIsConvertedAttribute(): bool
    {
        return $this->status === 'convertida_venta';
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        if (!$this->expiration_date || $this->status !== 'activa') return false;
        return $this->expiration_date->isFuture() &&
            $this->expiration_date->diffInDays(now()) <= 7;
    }

    public function getDaysUntilExpirationAttribute(): int
    {
        if (!$this->expiration_date) return 0;
        return $this->expiration_date->diffInDays(now(), false);
    }

    public function getFormattedReservationAmountAttribute(): string
    {
        return number_format($this->reservation_amount, 2);
    }

    public function getFormattedReservationPercentageAttribute(): string
    {
        return number_format($this->reservation_percentage, 2) . '%';
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'activa' => 'green',
            'confirmada' => 'blue',
            'cancelada' => 'red',
            'vencida' => 'gray',
            'convertida_venta' => 'purple',
            default => 'gray'
        };
    }

    public function getPaymentStatusColorAttribute(): string
    {
        return match ($this->payment_status) {
            'pendiente' => 'yellow',
            'pagado' => 'green',
            'parcial' => 'blue',
            default => 'gray'
        };
    }

    // Métodos
    public function confirm(): bool
    {
        if ($this->status === 'activa') {
            $this->update(['status' => 'confirmada']);
            return true;
        }
        return false;
    }

    public function cancel(string $reason = null): bool
    {
        if (in_array($this->status, ['activa', 'confirmada'])) {
            $this->update([
                'status' => 'cancelada',
                'notes' => $this->notes . "\n\nCancelada: " . $reason
            ]);

            // Liberar la unidad
            if ($this->unit) {
                $this->unit->unblock();
            }

            return true;
        }
        return false;
    }

    public function markAsExpired(): bool
    {
        if ($this->status === 'activa' && $this->is_expired) {
            $this->update(['status' => 'vencida']);

            // Liberar la unidad
            if ($this->unit) {
                $this->unit->unblock();
            }

            return true;
        }
        return false;
    }

    public function convertToSale(): bool
    {
        if (in_array($this->status, ['activa', 'confirmada'])) {
            $this->update(['status' => 'convertida_venta']);
            return true;
        }
        return false;
    }

    public function extendExpiration(\DateTime $newExpirationDate): bool
    {
        if ($this->status === 'activa') {
            $this->update(['expiration_date' => $newExpirationDate]);
            return true;
        }
        return false;
    }

    public function updatePaymentStatus(string $newStatus): void
    {
        $this->update(['payment_status' => $newStatus]);
    }

    public function addPaymentReference(string $reference): void
    {
        $this->update(['payment_reference' => $reference]);
    }

    public function signByClient(): void
    {
        $this->update(['client_signature' => true]);
    }

    public function signByAdvisor(): void
    {
        $this->update(['advisor_signature' => true]);
    }

    public function isFullySigned(): bool
    {
        return $this->client_signature && $this->advisor_signature;
    }

    public function canBeConfirmed(): bool
    {
        return $this->status === 'activa' && $this->is_fully_signed;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['activa', 'confirmada']);
    }

    public function canBeConverted(): bool
    {
        return in_array($this->status, ['activa', 'confirmada']);
    }

    public function needsRenewal(): bool
    {
        return $this->is_expiring_soon && $this->status === 'activa';
    }

    public function getRenewalAmount(): float
    {
        // Lógica para calcular el monto de renovación
        return $this->reservation_amount * 0.1; // 10% del monto de reserva
    }

    public function generateReservationNumber(): string
    {
        $prefix = 'RES';
        $year = now()->format('Y');
        $sequence = str_pad($this->id, 6, '0', STR_PAD_LEFT);

        return "{$prefix}-{$year}-{$sequence}";
    }
}
