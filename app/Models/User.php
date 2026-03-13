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
        'provider',
        'provider_id',
        'avatar',
        'access_token',
        'refresh_token',
        'token_expires',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'access_token',
        'refresh_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'token_expires'     => 'datetime',
        'password'          => 'hashed',
    ];

    public function hasValidToken(): bool
    {
        if (! $this->token_expires) {
            return (bool) $this->access_token;
        }
        return $this->token_expires->isFuture();
    }
}