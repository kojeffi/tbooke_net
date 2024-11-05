<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'user_id',
        'message',
        'admin_id',
        'is_read',
    ];

    // Relationship to the AdminMessage model
    public function message()
    {
        return $this->belongsTo(AdminMessage::class, 'message_id');
    }

    // Relationship to the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
