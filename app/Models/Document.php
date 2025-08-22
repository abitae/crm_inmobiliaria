<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'document_type', // contrato, factura, recibo, documento_legal, otros
        'category', // venta, alquiler, legal, marketing, otros
        'file_path',
        'file_name',
        'file_size', // tamaño en bytes
        'file_extension',
        'mime_type',
        'version',
        'is_current_version',
        'status', // borrador, revisado, aprobado, firmado, archivado
        'client_id',
        'project_id',
        'unit_id',
        'opportunity_id',
        'activity_id',
        'created_by',
        'updated_by',
        'reviewed_by',
        'reviewed_at',
        'approved_by',
        'approved_at',
        'signed_by',
        'signed_at',
        'expiration_date',
        'tags',
        'notes',
    ];

    protected $casts = [
        'client_id' => 'integer',
        'project_id' => 'integer',
        'unit_id' => 'integer',
        'opportunity_id' => 'integer',
        'activity_id' => 'integer',
        'file_size' => 'integer',
        'version' => 'integer',
        'is_current_version' => 'boolean',
        'reviewed_by' => 'integer',
        'reviewed_at' => 'datetime',
        'approved_by' => 'integer',
        'approved_at' => 'datetime',
        'signed_by' => 'integer',
        'signed_at' => 'datetime',
        'expiration_date' => 'date',
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

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function signedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signed_by');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(DocumentComment::class);
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(DocumentSignature::class);
    }

    // Scopes
    public function scopeCurrentVersion($query)
    {
        return $query->where('is_current_version', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
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

    public function scopeByOpportunity($query, $opportunityId)
    {
        return $query->where('opportunity_id', $opportunityId);
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiration_date', '>=', now())
            ->where('expiration_date', '<=', now()->addDays($days));
    }

    public function scopeExpired($query)
    {
        return $query->where('expiration_date', '<', now());
    }

    public function scopeByTag($query, $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    // Accessors
    public function getIsDraftAttribute(): bool
    {
        return $this->status === 'borrador';
    }

    public function getIsReviewedAttribute(): bool
    {
        return $this->status === 'revisado';
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'aprobado';
    }

    public function getIsSignedAttribute(): bool
    {
        return $this->status === 'firmado';
    }

    public function getIsArchivedAttribute(): bool
    {
        return $this->status === 'archivado';
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiration_date && $this->expiration_date->isPast();
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        if (!$this->expiration_date) return false;
        return $this->expiration_date->isFuture() &&
            $this->expiration_date->diffInDays(now()) <= 30;
    }

    public function getFileSizeInMBAttribute(): float
    {
        return round($this->file_size / (1024 * 1024), 2);
    }

    public function getFileSizeInKBAttribute(): float
    {
        return round($this->file_size / 1024, 2);
    }

    public function getFullFileNameAttribute(): string
    {
        return $this->file_name . '.' . $this->file_extension;
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'borrador' => 'gray',
            'revisado' => 'blue',
            'aprobado' => 'green',
            'firmado' => 'purple',
            'archivado' => 'black',
            default => 'gray'
        };
    }

    // Métodos
    public function review(int $reviewerId): bool
    {
        if ($this->status === 'borrador') {
            $this->update([
                'status' => 'revisado',
                'reviewed_by' => $reviewerId,
                'reviewed_at' => now()
            ]);
            return true;
        }
        return false;
    }

    public function approve(int $approverId): bool
    {
        if ($this->status === 'revisado') {
            $this->update([
                'status' => 'aprobado',
                'approved_by' => $approverId,
                'approved_at' => now()
            ]);
            return true;
        }
        return false;
    }

    public function sign(int $signerId): bool
    {
        if ($this->status === 'aprobado') {
            $this->update([
                'status' => 'firmado',
                'signed_by' => $signerId,
                'signed_at' => now()
            ]);
            return true;
        }
        return false;
    }

    public function archive(): bool
    {
        if (in_array($this->status, ['firmado', 'aprobado'])) {
            $this->update(['status' => 'archivado']);
            return true;
        }
        return false;
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

    public function canBeReviewed(): bool
    {
        return $this->status === 'borrador';
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'revisado';
    }

    public function canBeSigned(): bool
    {
        return $this->status === 'aprobado';
    }

    public function canBeArchived(): bool
    {
        return in_array($this->status, ['firmado', 'aprobado']);
    }

    public function isReadOnly(): bool
    {
        return in_array($this->status, ['firmado', 'archivado']);
    }

    public function getNextStatus(): ?string
    {
        return match ($this->status) {
            'borrador' => 'revisado',
            'revisado' => 'aprobado',
            'aprobado' => 'firmado',
            'firmado' => 'archivado',
            default => null
        };
    }

    public function canAdvanceStatus(): bool
    {
        return $this->getNextStatus() !== null;
    }
}
