<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Customer extends Authenticatable
{
    //
    use Notifiable, HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'online',
        'last_seen_at',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'online' => 'boolean'
    ];     
}
