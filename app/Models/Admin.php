<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $guard = 'admin';

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function receivedAdminMessages()
    {
        return $this->hasMany(AdminMessage::class, 'receiver_id');
    }
    public function sentAdminMessages()
    {
        return $this->hasMany(AdminMessage::class, 'sender_id');
    }
}
