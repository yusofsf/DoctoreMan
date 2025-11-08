<?php

namespace App\Models;

use App\Enums\AdminStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Model
{
    /** @use HasFactory<\Database\Factories\AdminFactory> */
    use HasFactory, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'display_name',
        'description',
        'status',
        'last_login_at',
        'last_login_ip',
        'user_id'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'last_login_at' => 'datetime',
        'status' => AdminStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Default attribute values.
     */
    protected $attributes = [
        'status' => AdminStatus::ACTIVE,
    ];

    /**
     * Hook into the model lifecycle to set initial last login values on create.
     */
    protected static function booted(): void
    {
        static::creating(function (self $admin): void {
            if (empty($admin->last_login_at)) {
                $admin->last_login_at = now();
            }

            if (empty($admin->last_login_ip)) {
                $admin->last_login_ip = request()?->ip() ?? '127.0.0.1';
            }
        });
    }

    /**
     * Get the user that owns the admin.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Update last login information.
     */
    public function updateLastLogin($ip = null)
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip ?? request()->ip(),
        ]);
    }
}
