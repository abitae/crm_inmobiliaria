<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'advisor_id',
        'project_id',
        'unit_id',
        'opportunity_id',
        'commission_type', // venta, reserva, seguimiento, bono
        'base_amount', // monto base para el cálculo
        'commission_percentage', // porcentaje de comisión
        'commission_amount', // monto de comisión calculado
        'bonus_amount', // monto de bono adicional
        'total_commission', // comisión total (comisión + bono)
        'status', // pendiente, aprobada, pagada, cancelada
        'payment_date', // fecha de pago
        'payment_method', // método de pago
        'payment_reference', // referencia de pago
        'notes',
        'approved_by',
        'approved_at',
        'paid_by',
        'paid_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'advisor_id' => 'integer',
        'project_id' => 'integer',
        'unit_id' => 'integer',
        'opportunity_id' => 'integer',
        'base_amount' => 'decimal:2',
        'commission_percentage' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'bonus_amount' => 'decimal:2',
        'total_commission' => 'decimal:2',
        'payment_date' => 'date',
        'approved_by' => 'integer',
        'approved_at' => 'datetime',
        'paid_by' => 'integer',
        'paid_at' => 'datetime',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relaciones
    public function advisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
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
    public function scopePending($query)
    {
        return $query->where('status', 'pendiente');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'aprobada');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'pagada');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelada');
    }

    public function scopeByAdvisor($query, $advisorId)
    {
        return $query->where('advisor_id', $advisorId);
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('commission_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('created_at', now()->year);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['pendiente', 'aprobada']);
    }

    // Accessors
    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pendiente';
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'aprobada';
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'pagada';
    }

    public function getIsCancelledAttribute(): bool
    {
        return $this->status === 'cancelada';
    }

    public function getIsUnpaidAttribute(): bool
    {
        return in_array($this->status, ['pendiente', 'aprobada']);
    }

    public function getFormattedBaseAmountAttribute(): string
    {
        return number_format($this->base_amount, 2);
    }

    public function getFormattedCommissionAmountAttribute(): string
    {
        return number_format($this->commission_amount, 2);
    }

    public function getFormattedTotalCommissionAttribute(): string
    {
        return number_format($this->total_commission, 2);
    }

    public function getFormattedBonusAmountAttribute(): string
    {
        return number_format($this->bonus_amount, 2);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pendiente' => 'yellow',
            'aprobada' => 'blue',
            'pagada' => 'green',
            'cancelada' => 'red',
            default => 'gray'
        };
    }

    // Métodos
    public function calculateCommission(): void
    {
        $this->commission_amount = round(($this->base_amount * $this->commission_percentage) / 100, 2);
        $this->total_commission = $this->commission_amount + $this->bonus_amount;
        $this->save();
    }

    public function addBonus(float $bonusAmount): void
    {
        $this->bonus_amount = $bonusAmount;
        $this->total_commission = $this->commission_amount + $this->bonus_amount;
        $this->save();
    }

    public function approve(int $approverId): bool
    {
        if ($this->status === 'pendiente') {
            $this->update([
                'status' => 'aprobada',
                'approved_by' => $approverId,
                'approved_at' => now()
            ]);
            return true;
        }
        return false;
    }

    public function pay(int $payerId, string $paymentMethod = null, string $paymentReference = null): bool
    {
        if ($this->status === 'aprobada') {
            $this->update([
                'status' => 'pagada',
                'payment_date' => now(),
                'payment_method' => $paymentMethod,
                'payment_reference' => $paymentReference,
                'paid_by' => $payerId,
                'paid_at' => now()
            ]);
            return true;
        }
        return false;
    }

    public function cancel(string $reason = null): bool
    {
        if (in_array($this->status, ['pendiente', 'aprobada'])) {
            $this->update([
                'status' => 'cancelada',
                'notes' => $this->notes . "\n\nCancelada: " . $reason
            ]);
            return true;
        }
        return false;
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'pendiente';
    }

    public function canBePaid(): bool
    {
        return $this->status === 'aprobada';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pendiente', 'aprobada']);
    }

    public function getCommissionRate(): float
    {
        return $this->commission_percentage;
    }

    public function getEffectiveRate(): float
    {
        if ($this->base_amount == 0) return 0;
        return round(($this->total_commission / $this->base_amount) * 100, 2);
    }

    public function isHighValue(): bool
    {
        return $this->base_amount >= 100000; // $100,000 o más
    }

    public function isLowValue(): bool
    {
        return $this->base_amount < 50000; // Menos de $50,000
    }
}
