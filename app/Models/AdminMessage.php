<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sender_id',
        'admin_id',
        'receiver_id',
        'subject',
        'message',
        'is_read',
    ];

    // Relationship to the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship to the AdminReply model
    public function replies()
    {
        return $this->hasMany(AdminReply::class, 'message_id');
    }

     public function sender()
     {
         return $this->belongsTo(User::class, 'sender_id');
     }
     public function receiver()
     {
         return $this->belongsTo(Admin::class, 'receiver_id');
     }

}

