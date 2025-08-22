<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'unit_number',
        'unit_type', // lote, casa, departamento, oficina, local
        'floor',
        'tower',
        'block',
        'area', // área en m²
        'bedrooms',
        'bathrooms',
        'parking_spaces',
        'storage_rooms',
        'balcony_area',
        'terrace_area',
        'garden_area',
        'status', // disponible, reservado, vendido, bloqueado, en_construccion
        'base_price', // precio base por m²
        'total_price', // precio total
        'discount_percentage',
        'discount_amount',
        'final_price', // precio final después de descuentos
        'commission_percentage',
        'commission_amount',
        'blocked_until', // fecha hasta cuando está bloqueado
        'blocked_by',
        'blocked_reason',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'project_id' => 'integer',
        'floor' => 'integer',
        'area' => 'decimal:2',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'parking_spaces' => 'integer',
        'storage_rooms' => 'integer',
        'balcony_area' => 'decimal:2',
        'terrace_area' => 'decimal:2',
        'garden_area' => 'decimal:2',
        'base_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_price' => 'decimal:2',
        'commission_percentage' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'blocked_until' => 'datetime',
        'blocked_by' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relaciones
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function blockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocked_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'client_unit_interests')
            ->withPivot('interest_level', 'notes', 'created_at')
            ->withTimestamps();
    }

    public function prices(): HasMany
    {
        return $this->hasMany(UnitPrice::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'disponible');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('unit_type', $type);
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('final_price', [$minPrice, $maxPrice]);
    }

    public function scopeByAreaRange($query, $minArea, $maxArea)
    {
        return $query->whereBetween('area', [$minArea, $maxArea]);
    }

    public function scopeByBedrooms($query, $bedrooms)
    {
        return $query->where('bedrooms', '>=', $bedrooms);
    }

    public function scopeNotBlocked($query)
    {
        return $query->where(function ($q) {
            $q->where('status', '!=', 'bloqueado')
                ->orWhere('blocked_until', '<', now());
        });
    }

    // Accessors
    public function getFullIdentifierAttribute(): string
    {
        $parts = array_filter([
            $this->project->name,
            $this->tower ? "Torre {$this->tower}" : null,
            $this->block ? "Bloque {$this->block}" : null,
            $this->floor ? "Piso {$this->floor}" : null,
            "Unidad {$this->unit_number}"
        ]);
        return implode(' - ', $parts);
    }

    public function getTotalAreaAttribute(): float
    {
        return $this->area + $this->balcony_area + $this->terrace_area + $this->garden_area;
    }

    public function getPricePerSquareMeterAttribute(): float
    {
        if ($this->area == 0) return 0;
        return round($this->final_price / $this->area, 2);
    }

    public function getDiscountAmountAttribute(): float
    {
        if ($this->discount_percentage > 0) {
            return round(($this->total_price * $this->discount_percentage) / 100, 2);
        }
        return $this->discount_amount ?? 0;
    }

    public function getIsBlockedAttribute(): bool
    {
        return $this->status === 'bloqueado' &&
            $this->blocked_until &&
            $this->blocked_until->isFuture();
    }

    // Métodos
    public function calculateFinalPrice(): void
    {
        $discountAmount = $this->getDiscountAmountAttribute();
        $this->final_price = $this->total_price - $discountAmount;
        $this->save();
    }

    public function calculateCommission(): void
    {
        if ($this->commission_percentage > 0) {
            $this->commission_amount = round(($this->final_price * $this->commission_percentage) / 100, 2);
            $this->save();
        }
    }

    public function reserve(): bool
    {
        if ($this->status === 'disponible' && !$this->is_blocked) {
            $this->update(['status' => 'reservado']);
            $this->project->updateUnitCounts();
            return true;
        }
        return false;
    }

    public function sell(): bool
    {
        if (in_array($this->status, ['disponible', 'reservado'])) {
            $this->update(['status' => 'vendido']);
            $this->project->updateUnitCounts();
            return true;
        }
        return false;
    }

    public function block(string $reason, \DateTime $until, int $blockedBy): bool
    {
        if ($this->status === 'disponible') {
            $this->update([
                'status' => 'bloqueado',
                'blocked_reason' => $reason,
                'blocked_until' => $until,
                'blocked_by' => $blockedBy
            ]);
            $this->project->updateUnitCounts();
            return true;
        }
        return false;
    }

    public function unblock(): bool
    {
        if ($this->status === 'bloqueado') {
            $this->update([
                'status' => 'disponible',
                'blocked_reason' => null,
                'blocked_until' => null,
                'blocked_by' => null
            ]);
            $this->project->updateUnitCounts();
            return true;
        }
        return false;
    }

    public function isAvailable(): bool
    {
        return $this->status === 'disponible' && !$this->is_blocked;
    }

    public function canBeReserved(): bool
    {
        return $this->isAvailable() && $this->project->canAcceptReservations();
    }

    public function canBeSold(): bool
    {
        return in_array($this->status, ['disponible', 'reservado']);
    }
}
