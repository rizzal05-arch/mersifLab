<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_subscriber',
        'subscription_expires_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'subscription_expires_at' => 'datetime',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSubscriber(): bool
    {
        return $this->is_subscriber &&
               ($this->subscription_expires_at === null ||
                $this->subscription_expires_at > now());
    }
}