<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Opportunity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',// id del cliente
        'project_id',// id del proyecto
        'unit_id',// id de la unidad
        'advisor_id',// id del asesor
        'stage', // captado, calificado, contacto, propuesta, visita, negociacion, cierre, cancelada
        'status', // activa, ganada, perdida, cancelada
        'probability', // porcentaje de probabilidad de cierre, 0-100
        'expected_value', // valor esperado de la venta, 0-1000000,
        'expected_close_date', // fecha esperada de cierre, YYYY-MM-DD
        'actual_close_date', // fecha real de cierre, YYYY-MM-DD
        'close_value', // valor real de la venta, 0-1000000
        'close_reason', // razón del cierre
        'lost_reason', // razón de la pérdida
        'notes',// notas de la oportunidad
        'source', // origen de la oportunidad
        'campaign', // campaña asociada
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'client_id' => 'integer',
        'project_id' => 'integer',
        'unit_id' => 'integer',
        'advisor_id' => 'integer',
        'probability' => 'integer',
        'expected_value' => 'decimal:2',
        'expected_close_date' => 'date',
        'actual_close_date' => 'date',
        'close_value' => 'decimal:2',
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

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'activa');
    }

    public function scopeByStage($query, $stage)
    {
        return $query->where('stage', $stage);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByAdvisor($query, $advisorId)
    {
        return $query->where('advisor_id', $advisorId);
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeWon($query)
    {
        return $query->where('status', 'ganada');
    }

    public function scopeLost($query)
    {
        return $query->where('status', 'perdida');
    }

    public function scopeClosingThisMonth($query)
    {
        return $query->where('expected_close_date', '>=', now()->startOfMonth())
            ->where('expected_close_date', '<=', now()->endOfMonth());
    }

    public function scopeOverdue($query)
    {
        return $query->where('expected_close_date', '<', now())
            ->where('status', 'activa');
    }

    // Nuevos scopes optimizados
    public function scopeBySource($query, $source)
    {
        return $query->where('source', $source);
    }

    public function scopeByCampaign($query, $campaign)
    {
        return $query->where('campaign', $campaign);
    }

    public function scopeByProbabilityRange($query, $min, $max)
    {
        return $query->whereBetween('probability', [$min, $max]);
    }

    public function scopeByValueRange($query, $min, $max)
    {
        return $query->whereBetween('expected_value', [$min, $max]);
    }

    public function scopeByDateRange($query, $from, $to)
    {
        return $query->whereBetween('expected_close_date', [$from, $to]);
    }

    public function scopeByCreatedDateRange($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    public function scopeHighValue($query, $minValue = 100000)
    {
        return $query->where('expected_value', '>=', $minValue);
    }

    public function scopeHighProbability($query, $minProbability = 80)
    {
        return $query->where('probability', '>=', $minProbability);
    }

    public function scopeClosingSoon($query, $days = 30)
    {
        return $query->where('expected_close_date', '<=', now()->addDays($days))
                    ->where('status', 'activa');
    }

    public function scopeRecentlyCreated($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeRecentlyUpdated($query, $days = 7)
    {
        return $query->where('updated_at', '>=', now()->subDays($days));
    }

    // Accessors
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'activa';
    }

    public function getIsWonAttribute(): bool
    {
        return $this->status === 'ganada';
    }

    public function getIsLostAttribute(): bool
    {
        return $this->status === 'perdida';
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->expected_close_date && $this->expected_close_date->isPast() && $this->is_active;
    }

    public function getDaysUntilCloseAttribute(): int
    {
        if (!$this->expected_close_date) return 0;
        return now()->diffInDays($this->expected_close_date, false);
    }

    public function getWeightedValueAttribute(): float
    {
        return round(($this->expected_value * $this->probability) / 100, 2);
    }

    // Métodos
    public function advanceStage(string $newStage): bool
    {
        $validStages = ['captado', 'calificado', 'contacto', 'propuesta', 'visita', 'negociacion', 'cierre'];
        $currentIndex = array_search($this->stage, $validStages);
        $newIndex = array_search($newStage, $validStages);

        if ($newIndex !== false && $newIndex > $currentIndex) {
            $this->update(['stage' => $newStage]);
            return true;
        }
        return false;
    }

    public function markAsWon(float $closeValue, string $closeReason = null): bool
    {
        if ($this->status === 'activa') {
            $this->update([
                'status' => 'ganada',
                'close_value' => $closeValue,
                'close_reason' => $closeReason,
                'actual_close_date' => now(),
                'probability' => 100
            ]);
            return true;
        }
        return false;
    }

    public function markAsLost(string $lostReason): bool
    {
        if ($this->status === 'activa') {
            $this->update([
                'status' => 'perdida',
                'lost_reason' => $lostReason,
                'actual_close_date' => now(),
                'probability' => 0
            ]);
            return true;
        }
        return false;
    }

    public function cancel(string $reason = null): bool
    {
        if ($this->status === 'activa') {
            $this->update([
                'status' => 'cancelada',
                'lost_reason' => $reason,
                'actual_close_date' => now()
            ]);
            return true;
        }
        return false;
    }

    public function updateProbability(int $newProbability): void
    {
        $this->update(['probability' => max(0, min(100, $newProbability))]);
    }

    public function updateExpectedValue(float $newValue): void
    {
        $this->update(['expected_value' => $newValue]);
    }

    public function updateExpectedCloseDate(\DateTime $newDate): void
    {
        $this->update(['expected_close_date' => $newDate]);
    }

    public function assignAdvisor(int $advisorId): void
    {
        $this->update(['advisor_id' => $advisorId]);
    }

    public function canAdvanceToStage(string $stage): bool
    {
        $validStages = ['captado', 'calificado', 'contacto', 'propuesta', 'visita', 'negociacion', 'cierre'];
        $currentIndex = array_search($this->stage, $validStages);
        $targetIndex = array_search($stage, $validStages);

        return $targetIndex !== false && $targetIndex > $currentIndex;
    }

    public function getNextStage(): ?string
    {
        $validStages = ['captado', 'calificado', 'contacto', 'propuesta', 'visita', 'negociacion', 'cierre'];
        $currentIndex = array_search($this->stage, $validStages);

        if ($currentIndex !== false && $currentIndex < count($validStages) - 1) {
            return $validStages[$currentIndex + 1];
        }

        return null;
    }
}
