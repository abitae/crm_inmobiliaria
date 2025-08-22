<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Interaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'opportunity_id',
        'interaction_type', // llamada, email, mensaje, visita, reunion, otros
        'channel', // telefonico, email, whatsapp, presencial, redes_sociales
        'direction', // entrada, salida, bidireccional
        'subject',
        'content',
        'duration', // duración en minutos (para llamadas)
        'status', // programada, en_progreso, completada, cancelada
        'priority', // baja, media, alta, urgente
        'scheduled_at',
        'completed_at',
        'result',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'client_id' => 'integer',
        'opportunity_id' => 'integer',
        'duration' => 'integer',
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
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

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
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
    public function scopeByType($query, $type)
    {
        return $query->where('interaction_type', $type);
    }

    public function scopeByChannel($query, $channel)
    {
        return $query->where('channel', $channel);
    }

    public function scopeByDirection($query, $direction)
    {
        return $query->where('direction', $direction);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeByOpportunity($query, $opportunityId)
    {
        return $query->where('opportunity_id', $opportunityId);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'programada');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completada');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('scheduled_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('scheduled_at', [$startDate, $endDate]);
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

    public function getDurationInMinutesAttribute(): int
    {
        return $this->duration ?? 0;
    }

    public function getFormattedDurationAttribute(): string
    {
        if (!$this->duration) return '0 min';

        $hours = intval($this->duration / 60);
        $minutes = $this->duration % 60;

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$minutes}m";
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
                'completed_at' => now()
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

    public function reschedule(\DateTime $newScheduledAt): bool
    {
        if ($this->status === 'programada') {
            $this->update(['scheduled_at' => $newScheduledAt]);
            return true;
        }
        return false;
    }

    public function canBeStarted(): bool
    {
        return $this->status === 'programada' && $this->scheduled_at && $this->scheduled_at->isPast();
    }

    public function canBeCompleted(): bool
    {
        return in_array($this->status, ['programada', 'en_progreso']);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['programada', 'en_progreso']);
    }

    public function isOverdue(): bool
    {
        return $this->scheduled_at && $this->scheduled_at->isPast() &&
            in_array($this->status, ['programada', 'en_progreso']);
    }
}
