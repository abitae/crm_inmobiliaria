<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reservation) {
            if (empty($reservation->reservation_number)) {
                $reservation->reservation_number = $reservation->generateReservationNumber();
            }
        });
    }

    protected $fillable = [
        'client_id', // id del cliente (client_id)
        'project_id', // id del proyecto (project_id)
        'unit_id', // id de la unidad (unit_id)
        'advisor_id', // id del asesor (user_id)
        'reservation_number', // número de la reserva
        'reservation_type', // pre_reserva, reserva_firmada, reserva_confirmada
        'status', // activa, confirmada, cancelada, vencida, convertida_venta
        'reservation_date', // fecha de la reserva
        'expiration_date', // fecha de vencimiento de la reserva
        'reservation_amount', // monto de la reserva
        'reservation_percentage', // porcentaje del precio total
        'payment_method', // efectivo, transferencia, tarjeta de crédito, tarjeta de débito, paypal, mercado pago, otros
        'payment_status', // pendiente, pagado, parcial
        'payment_reference', // referencia de la transacción
        'notes', // notas de la reserva
        'terms_conditions', // términos y condiciones de la reserva
        'image', // imagen de la reserva
        'client_signature', // firma del cliente
        'advisor_signature', // firma del asesor
        'created_by', // usuario que creó la reserva (user_id)
        'updated_by', // usuario que actualizó la reserva (user_id)
    ];

    protected $casts = [
        'client_id' => 'integer', // id del cliente (client_id)
        'project_id' => 'integer', // id del proyecto (project_id)
        'unit_id' => 'integer', // id de la unidad (unit_id)
        'advisor_id' => 'integer', // id del asesor (user_id)
        'reservation_amount' => 'decimal:2', // monto de la reserva
        'reservation_percentage' => 'decimal:2', // porcentaje del precio total
        'reservation_date' => 'datetime', // fecha de la reserva
        'expiration_date' => 'datetime', // fecha de vencimiento de la reserva
        'client_signature' => 'boolean', // firma del cliente
        'advisor_signature' => 'boolean', // firma del asesor
        'created_by' => 'integer', // usuario que creó la reserva (user_id)
        'updated_by' => 'integer', // usuario que actualizó la reserva (user_id)
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
        return number_format((float)($this->reservation_amount ?? 0), 2);
    }

    public function getFormattedReservationPercentageAttribute(): string
    {
        return number_format((float)($this->reservation_percentage ?? 0), 2) . '%';
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

            // Actualizar la unidad a 'disponible'
            if ($this->unit) {
                $this->unit->update(['status' => 'disponible']);
                $this->unit->project->updateUnitCounts();
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
        
        // Obtener el último número de reserva del año actual
        $lastReservation = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastReservation && $lastReservation->reservation_number) {
            // Extraer el número de secuencia del último número
            $parts = explode('-', $lastReservation->reservation_number);
            $lastSequence = isset($parts[2]) ? (int)$parts[2] : 0;
            $sequence = str_pad($lastSequence + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $sequence = '000001';
        }

        return "{$prefix}-{$year}-{$sequence}";
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }
        
        if (Str::startsWith($this->image, 'http')) {
            return $this->image;
        }
        
        return asset('storage/' . $this->image);
    }
}
