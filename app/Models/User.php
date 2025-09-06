<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
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
        'lider_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
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
            'lider_id' => 'integer',
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
     * Get users by role
     */
    public static function byRole(string $role): \Illuminate\Database\Eloquent\Builder
    {
        return static::role($role);
    }

    /**
     * Get users who can be assigned as advisors (admin, lider, and vendedor roles)
     */
    public static function getAvailableAdvisors(User $user): \Illuminate\Database\Eloquent\Collection
    {
        if ($user->isAdmin()) {
            return static::role(['admin', 'lider', 'vendedor'])
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
        return new \Illuminate\Database\Eloquent\Collection();
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
}
