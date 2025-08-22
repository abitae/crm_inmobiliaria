<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'task_type', // seguimiento, visita, llamada, documento, otros
        'priority', // baja, media, alta, urgente
        'status', // pendiente, en_progreso, completada, cancelada
        'due_date',
        'completed_at',
        'estimated_hours',
        'actual_hours',
        'client_id',
        'project_id',
        'unit_id',
        'opportunity_id',
        'assigned_to',
        'created_by',
        'updated_by',
        'notes',
        'tags',
    ];

    protected $casts = [
        'client_id' => 'integer',
        'project_id' => 'integer',
        'unit_id' => 'integer',
        'opportunity_id' => 'integer',
        'assigned_to' => 'integer',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
        'tags' => 'array',
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

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
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

    public function scopeInProgress($query)
    {
        return $query->where('status', 'en_progreso');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completada');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelada');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('task_type', $type);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeDueToday($query)
    {
        return $query->whereDate('due_date', today());
    }

    public function scopeDueThisWeek($query)
    {
        return $query->whereBetween('due_date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->whereIn('status', ['pendiente', 'en_progreso']);
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['alta', 'urgente']);
    }

    public function scopeByTag($query, $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    // Accessors
    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pendiente';
    }

    public function getIsInProgressAttribute(): bool
    {
        return $this->status === 'en_progreso';
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completada';
    }

    public function getIsCancelledAttribute(): bool
    {
        return $this->status === 'cancelada';
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date && $this->due_date->isPast() &&
            in_array($this->status, ['pendiente', 'en_progreso']);
    }

    public function getIsDueTodayAttribute(): bool
    {
        return $this->due_date && $this->due_date->isToday();
    }

    public function getIsHighPriorityAttribute(): bool
    {
        return in_array($this->priority, ['alta', 'urgente']);
    }

    public function getDaysUntilDueAttribute(): int
    {
        if (!$this->due_date) return 0;
        return $this->due_date->diffInDays(now(), false);
    }

    public function getFormattedEstimatedHoursAttribute(): string
    {
        return number_format($this->estimated_hours, 2) . 'h';
    }

    public function getFormattedActualHoursAttribute(): string
    {
        return number_format($this->actual_hours, 2) . 'h';
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pendiente' => 'yellow',
            'en_progreso' => 'blue',
            'completada' => 'green',
            'cancelada' => 'red',
            default => 'gray'
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'baja' => 'green',
            'media' => 'yellow',
            'alta' => 'orange',
            'urgente' => 'red',
            default => 'gray'
        };
    }

    // Métodos
    public function start(): bool
    {
        if ($this->status === 'pendiente') {
            $this->update(['status' => 'en_progreso']);
            return true;
        }
        return false;
    }

    public function complete(float $actualHours = null): bool
    {
        if (in_array($this->status, ['pendiente', 'en_progreso'])) {
            $this->update([
                'status' => 'completada',
                'completed_at' => now(),
                'actual_hours' => $actualHours ?? $this->estimated_hours
            ]);
            return true;
        }
        return false;
    }

    public function cancel(string $reason = null): bool
    {
        if (in_array($this->status, ['pendiente', 'en_progreso'])) {
            $this->update([
                'status' => 'cancelada',
                'notes' => $this->notes . "\n\nCancelada: " . $reason
            ]);
            return true;
        }
        return false;
    }

    public function reassign(int $newUserId): void
    {
        $this->update(['assigned_to' => $newUserId]);
    }

    public function updateDueDate(\DateTime $newDueDate): void
    {
        $this->update(['due_date' => $newDueDate]);
    }

    public function addTag(string $tag): void
    {
        $tags = $this->tags ?? [];
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->update(['tags' => $tags]);
        }
    }

    public function removeTag(string $tag): void
    {
        $tags = $this->tags ?? [];
        $tags = array_filter($tags, fn($t) => $t !== $tag);
        $this->update(['tags' => array_values($tags)]);
    }

    public function hasTag(string $tag): bool
    {
        return in_array($tag, $this->tags ?? []);
    }

    public function canBeStarted(): bool
    {
        return $this->status === 'pendiente';
    }

    public function canBeCompleted(): bool
    {
        return in_array($this->status, ['pendiente', 'en_progreso']);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pendiente', 'en_progreso']);
    }

    public function needsAttention(): bool
    {
        return $this->is_overdue || $this->is_high_priority;
    }

    public function getProgressPercentage(): float
    {
        if ($this->status === 'completada') return 100;
        if ($this->status === 'cancelada') return 0;
        if ($this->status === 'en_progreso') return 50;
        return 0;
    }
}
