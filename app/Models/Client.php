<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'document_type', // DNI, RUC, CE, PASAPORTE
        'document_number',
        'address',
        'district',
        'province',
        'region',
        'country',
        'client_type', // inversor, comprador, empresa, constructor
        'source', // redes_sociales, ferias, referidos, formulario_web, publicidad
        'status', // nuevo, contacto_inicial, en_seguimiento, cierre, perdido
        'score', // scoring del lead
        'notes',
        'assigned_advisor_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'score' => 'integer',
        'assigned_advisor_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relaciones
    public function assignedAdvisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_advisor_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'client_project_interests')
            ->withPivot('interest_level', 'notes', 'created_at')
            ->withTimestamps();
    }

    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'client_unit_interests')
            ->withPivot('interest_level', 'notes', 'created_at')
            ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'perdido');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('client_type', $type);
    }

    public function scopeBySource($query, $source)
    {
        return $query->where('source', $source);
    }

    public function scopeByAdvisor($query, $advisorId)
    {
        return $query->where('assigned_advisor_id', $advisorId);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([$this->address, $this->district, $this->province, $this->region, $this->country]);
        return implode(', ', $parts);
    }

    public function getLastInteractionAttribute()
    {
        return $this->interactions()->latest()->first();
    }

    public function getFullDocumentAttribute(): string
    {
        if ($this->document_type && $this->document_number) {
            return $this->document_type . ': ' . $this->document_number;
        }
        return 'Sin documento';
    }

    public function getClientTypeFormattedAttribute(): string
    {
        $types = [
            'inversor' => 'Inversor',
            'comprador' => 'Comprador',
            'empresa' => 'Empresa',
            'constructor' => 'Constructor'
        ];

        return $types[$this->client_type] ?? ucfirst($this->client_type);
    }

    // Métodos
    public function updateScore(int $newScore): void
    {
        $this->update(['score' => $newScore]);
    }

    public function changeStatus(string $newStatus): void
    {
        $this->update(['status' => $newStatus]);
    }

    public function assignAdvisor(int $advisorId): void
    {
        $this->update(['assigned_advisor_id' => $advisorId]);
    }

    public function isActive(): bool
    {
        return !in_array($this->status, ['perdido', 'cierre']);
    }

    public function hasActiveOpportunities(): bool
    {
        return $this->opportunities()->where('status', '!=', 'perdida')->exists();
    }
}
