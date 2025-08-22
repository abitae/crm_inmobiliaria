<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'activity_type', // llamada, reunion, visita, seguimiento, tarea
        'status', // programada, en_progreso, completada, cancelada
        'priority', // baja, media, alta, urgente
        'start_date',
        'end_date',
        'duration', // duración en minutos
        'location',
        'client_id',
        'project_id',
        'unit_id',
        'opportunity_id',
        'advisor_id',
        'assigned_to', // ID del usuario asignado
        'reminder_before', // minutos antes para recordatorio
        'reminder_sent',
        'notes',
        'result',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'client_id' => 'integer',
        'project_id' => 'integer',
        'unit_id' => 'integer',
        'opportunity_id' => 'integer',
        'advisor_id' => 'integer',
        'assigned_to' => 'integer',
        'duration' => 'integer',
        'reminder_before' => 'integer',
        'reminder_sent' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
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

    public function advisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'advisor_id');
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

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    // Scopes
    public function scopeScheduled($query)
    {
        return $query->where('status', 'programada');
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
        return $query->where('activity_type', $type);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByAdvisor($query, $advisorId)
    {
        return $query->where('advisor_id', $advisorId);
    }

    public function scopeByAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('start_date', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('start_date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereBetween('start_date', [now()->startOfMonth(), now()->endOfMonth()]);
    }

    public function scopeUpcoming($query, $days = 7)
    {
        return $query->where('start_date', '>=', now())
            ->where('start_date', '<=', now()->addDays($days));
    }

    public function scopeOverdue($query)
    {
        return $query->where('start_date', '<', now())
            ->whereIn('status', ['programada', 'en_progreso']);
    }

    public function scopeWithReminders($query)
    {
        return $query->whereNotNull('reminder_before')
            ->where('reminder_sent', false);
    }

    // Accessors
    public function getIsScheduledAttribute(): bool
    {
        return $this->status === 'programada';
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
        return $this->start_date && $this->start_date->isPast() &&
            in_array($this->status, ['programada', 'en_progreso']);
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->start_date && $this->start_date->isFuture();
    }

    public function getDurationInHoursAttribute(): float
    {
        return round($this->duration / 60, 2);
    }

    public function getReminderTimeAttribute(): ?\DateTime
    {
        if (!$this->reminder_before || !$this->start_date) return null;
        return $this->start_date->copy()->subMinutes($this->reminder_before);
    }

    public function getShouldSendReminderAttribute(): bool
    {
        if (!$this->reminder_before || $this->reminder_sent) return false;

        $reminderTime = $this->getReminderTimeAttribute();
        return $reminderTime && $reminderTime->isPast();
    }

    // Métodos
    public function start(): bool
    {
        if ($this->status === 'programada') {
            $this->update(['status' => 'en_progreso']);
            return true;
        }
        return false;
    }

    public function complete(string $result = null): bool
    {
        if (in_array($this->status, ['programada', 'en_progreso'])) {
            $this->update([
                'status' => 'completada',
                'result' => $result,
                'end_date' => now()
            ]);
            return true;
        }
        return false;
    }

    public function cancel(string $reason = null): bool
    {
        if (in_array($this->status, ['programada', 'en_progreso'])) {
            $this->update([
                'status' => 'cancelada',
                'notes' => $this->notes . "\n\nCancelada: " . $reason
            ]);
            return true;
        }
        return false;
    }

    public function reschedule(\DateTime $newStartDate, \DateTime $newEndDate = null): bool
    {
        if ($this->status === 'programada') {
            $this->update([
                'start_date' => $newStartDate,
                'end_date' => $newEndDate ?? $newStartDate->copy()->addMinutes($this->duration)
            ]);
            return true;
        }
        return false;
    }

    public function assignTo(int $userId): void
    {
        $this->update(['assigned_to' => $userId]);
    }

    public function setReminder(int $minutesBefore): void
    {
        $this->update([
            'reminder_before' => $minutesBefore,
            'reminder_sent' => false
        ]);
    }

    public function markReminderSent(): void
    {
        $this->update(['reminder_sent' => true]);
    }

    public function isAssignedTo(int $userId): bool
    {
        return $this->assigned_to === $userId || $this->advisor_id === $userId;
    }

    public function canBeStarted(): bool
    {
        return $this->status === 'programada' && $this->start_date && $this->start_date->isPast();
    }

    public function canBeCompleted(): bool
    {
        return in_array($this->status, ['programada', 'en_progreso']);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['programada', 'en_progreso']);
    }
}
