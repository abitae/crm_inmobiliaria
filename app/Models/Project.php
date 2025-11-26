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
        'project_type', // lotes
        'is_published', // publicar o no en la página web
        'lote_type', // normal, express
        'stage', // preventa, lanzamiento, venta_activa, cierre
        'legal_status', // con_titulo, en_tramite, habilitado
        'address', // dirección del proyecto
        'district', // distrito del proyecto
        'province', // provincia del proyecto
        'region', // región del proyecto
        'country', // país del proyecto
        'ubicacion', // ubicación del proyecto (link de Google Maps)
        'total_units', // total de unidades del proyecto
        'available_units', // unidades disponibles del proyecto
        'reserved_units', // unidades reservadas del proyecto
        'sold_units', // unidades vendidas del proyecto
        'blocked_units', // unidades bloqueadas del proyecto
        'start_date', // fecha de inicio del proyecto
        'end_date', // fecha de fin del proyecto
        'delivery_date', // fecha de entrega del proyecto
        'status', // activo, inactivo, suspendido, finalizado
        'path_image_portada', // ruta de la imagen de portada del proyecto
        'path_video_portada', // ruta del video de portada del proyecto
        'path_images', // rutas de las imágenes del proyecto
        'path_videos', // rutas de los videos del proyecto
        'path_documents', // rutas de los documentos del proyecto
        'estado_legal', // -Derecho Posesorio-Compra y Venta-Juez de Paz-Titulo de propiedad
        'tipo_proyecto', // propio , tercero
        'tipo_financiamiento', // contado, financiado
        'banco', // banco del proyecto
        'tipo_cuenta', // cuenta corriente, cuenta vista, cuenta ahorro
        'cuenta_bancaria', // cuenta bancaria del proyecto
        'created_by', // usuario que creó el proyecto
        'updated_by', // usuario que actualizó el proyecto
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'total_units' => 'integer',
        'available_units' => 'integer',
        'reserved_units' => 'integer',
        'sold_units' => 'integer',
        'blocked_units' => 'integer',
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
        // Incluir vendidas, reservadas, transferidas y cuotas en el progreso
        $soldProgress = $this->sold_units + $this->reserved_units + $this->transferido_units + $this->cuotas_units;
        return round(($soldProgress / $this->total_units) * 100, 2);
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([$this->address, $this->district, $this->province, $this->region, $this->country]);
        return implode(', ', $parts);
    }

    public function getCoordinatesAttribute(): array
    {
        // Extraer coordenadas del link de Google Maps
        if ($this->ubicacion && preg_match('/q=([^&]+)/', $this->ubicacion, $matches)) {
            $coords = explode(',', $matches[1]);
            if (count($coords) === 2) {
                return [
                    'lat' => (float) trim($coords[0]),
                    'lng' => (float) trim($coords[1])
                ];
            }
        }
        
        return [
            'lat' => null,
            'lng' => null
        ];
    }

    // Métodos
    public function updateUnitCounts(): void
    {
        $this->update([
            'available_units' => $this->units()->where('status', 'disponible')->count(),
            'reserved_units' => $this->units()->where('status', 'reservado')->count(),
            'sold_units' => $this->units()->where('status', 'vendido')->count(),
            'blocked_units' => 0, // Ya no se usa, mantener para compatibilidad
        ]);
    }
    
    // Accessors para los nuevos estados
    public function getTransferidoUnitsAttribute(): int
    {
        return $this->units()->where('status', 'transferido')->count();
    }
    
    public function getCuotasUnitsAttribute(): int
    {
        return $this->units()->where('status', 'cuotas')->count();
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
