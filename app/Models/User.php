<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'dni',
        'pin',
        'lider_id',
        'is_active',
        'banco',
        'cuenta_bancaria',
        'cci_bancaria',
        'ocupacion',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'pin',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'pin' => 'hashed',
            'lider_id' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is lider
     */
    public function isLider(): bool
    {
        return $this->hasRole('lider');
    }
    /**
     * Check if user is advisor (vendedor)
     */
    public function isAdvisor(): bool
    {
        return $this->hasRole('vendedor');
    }

    /**
     * Check if user is datero (captador de datos)
     */
    public function isDatero(): bool
    {
        return $this->hasRole('datero');
    }

    /**
     * Check if user can access Cazador API
     * Permite acceso a: Administrador, Lider y Cazador (vendedor)
     * NO permite acceso a: Dateros
     */
    public function canAccessCazadorApi(): bool
    {
        return $this->isAdmin() || $this->isLider() || $this->isAdvisor();
    }

    /**
     * Get the user's single role
     */
    public function getRole(): ?\Spatie\Permission\Models\Role
    {
        return $this->roles->first();
    }

    /**
     * Get the user's role name
     */
    public function getRoleName(): ?string
    {
        $role = $this->getRole();
        return $role ? $role->name : null;
    }

    /**
     * Set a single role for the user (replaces any existing roles)
     */
    public function setRole(string $roleName): void
    {
        // Remove all existing roles
        $this->syncRoles([]);
        // Assign the new role
        $this->assignRole($roleName);
    }

    /**
     * Check if user has any role
     */
    public function hasAnyRole(): bool
    {
        return $this->roles->count() > 0;
    }

    /**
     * Check if user has no roles
     */
    public function hasNoRoles(): bool
    {
        return $this->roles->count() === 0;
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Check if user is inactive
     */
    public function isInactive(): bool
    {
        return $this->is_active === false;
    }

    /**
     * Activate user
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Deactivate user
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Toggle user active status
     */
    public function toggleActiveStatus(): void
    {
        $this->update(['is_active' => !$this->is_active]);
    }



    /**
     * Get users by role
     */
    public static function byRole(string $role): \Illuminate\Database\Eloquent\Builder
    {
        return static::role($role);
    }

    /**
     * Get users who can be assigned as advisors (admin, lider, vendedor, and datero roles)
     */
    public static function getAvailableAdvisors(User $user): \Illuminate\Database\Eloquent\Collection
    {
        if ($user->isAdmin()) {
            return static::role(['admin', 'lider', 'vendedor', 'datero'])
                ->get();
        }
        if ($user->isLider()) {
            return static::where('lider_id', $user->id)
                ->orWhere('id', $user->id)
                ->get();
        }
        if ($user->isAdvisor()) {
            return static::where('id', $user->id)->get();
        }
        if ($user->isDatero()) {
            return static::where('id', $user->id)->get();
        }
        return new \Illuminate\Database\Eloquent\Collection();
    }

    /**
     * Get all users with their single role
     */
    public static function withRole(): \Illuminate\Database\Eloquent\Builder
    {
        return static::with('roles');
    }

    /**
     * Get users by single role name
     */
    public static function bySingleRole(string $roleName): \Illuminate\Database\Eloquent\Builder
    {
        return static::whereHas('roles', function ($query) use ($roleName) {
            $query->where('name', $roleName);
        });
    }

    /**
     * Scope to get only active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only inactive users
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope to get users by active status
     */
    public function scopeByActiveStatus($query, bool $isActive)
    {
        return $query->where('is_active', $isActive);
    }

    /**
     * Get the leader assigned to this user
     */
    public function lider()
    {
        return $this->belongsTo(User::class, 'lider_id');
    }

    /**
     * Get users assigned to this user as leader
     */
    public function subordinados()
    {
        return $this->hasMany(User::class, 'lider_id');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->getRoleName(),
            'is_active' => $this->isActive(),
        ];
    }
}
