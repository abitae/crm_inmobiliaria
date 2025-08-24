<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'project_type', // lotes, casas, departamentos, oficinas, mixto
        'stage', // preventa, lanzamiento, venta_activa, cierre
        'legal_status', // con_titulo, en_tramite, habilitado
        'address',
        'district',
        'province',
        'region',
        'country',
        'latitude',
        'longitude',
        'total_units',
        'available_units',
        'reserved_units',
        'sold_units',
        'blocked_units',
        'start_date',
        'end_date',
        'delivery_date',
        'status', // activo, inactivo, suspendido, finalizado
        'path_images',
        'path_videos',
        'path_documents',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'total_units' => 'integer',
        'available_units' => 'integer',
        'reserved_units' => 'integer',
        'sold_units' => 'integer',
        'blocked_units' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'start_date' => 'date',
        'end_date' => 'date',
        'delivery_date' => 'date',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'path_images' => 'array',
        'path_videos' => 'array',
        'path_documents' => 'array',
    ];

    // Relaciones
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ProjectPrice::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class);
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'client_project_interests')
            ->withPivot('interest_level', 'notes', 'created_at')
            ->withTimestamps();
    }

    public function advisors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'advisor_project_assignments')
            ->withPivot('assigned_at', 'is_primary', 'notes')
            ->withTimestamps();
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'activo');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('project_type', $type);
    }

    public function scopeByStage($query, $stage)
    {
        return $query->where('stage', $stage);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByLocation($query, $district = null, $province = null, $region = null)
    {
        if ($district) {
            $query->where('district', $district);
        }
        if ($province) {
            $query->where('province', $province);
        }
        if ($region) {
            $query->where('region', $region);
        }
        return $query;
    }

    public function scopeWithAvailableUnits($query)
    {
        return $query->where('available_units', '>', 0);
    }

    // Accessors
    public function getProgressPercentageAttribute(): float
    {
        if ($this->total_units == 0) return 0;
        return round((($this->sold_units + $this->reserved_units) / $this->total_units) * 100, 2);
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([$this->address, $this->district, $this->province, $this->region, $this->country]);
        return implode(', ', $parts);
    }

    public function getCoordinatesAttribute(): array
    {
        return [
            'lat' => $this->latitude,
            'lng' => $this->longitude
        ];
    }

    // Métodos
    public function updateUnitCounts(): void
    {
        $this->update([
            'available_units' => $this->units()->where('status', 'disponible')->count(),
            'reserved_units' => $this->units()->where('status', 'reservado')->count(),
            'sold_units' => $this->units()->where('status', 'vendido')->count(),
            'blocked_units' => $this->units()->where('status', 'bloqueado')->count(),
        ]);
    }

    public function isActive(): bool
    {
        return $this->status === 'activo';
    }

    public function hasAvailableUnits(): bool
    {
        return $this->available_units > 0;
    }

    public function canAcceptReservations(): bool
    {
        return in_array($this->stage, ['preventa', 'lanzamiento', 'venta_activa']) && $this->isActive();
    }

    public function getCurrentPrice(): ?ProjectPrice
    {
        return $this->prices()->where('is_active', true)->first();
    }

    public function assignAdvisor(int $advisorId, bool $isPrimary = false, string $notes = null): void
    {
        $this->advisors()->attach($advisorId, [
            'assigned_at' => now(),
            'is_primary' => $isPrimary,
            'notes' => $notes
        ]);
    }

    public function removeAdvisor(int $advisorId): void
    {
        $this->advisors()->detach($advisorId);
    }
}
