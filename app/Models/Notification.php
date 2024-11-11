<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sender_id',
        'type',
        'follower_name',
        'message',
        'message_id',
        'admin_message_id',
        'read',
    ];

    /**
     * Get notifications for a specific user.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */

     public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public static function markAsRead($userId)
    {
        self::where('user_id', $userId)
        ->where('read', 0)
        ->where('type', 'New Connection')
        ->update([
            'read' => 1,
        ]);
    }

    public static function messagesmarkAsRead($userId)
    {
        self::where('user_id', $userId)
        ->where('read', 0)
        ->where('type', 'New Message')
        ->update([
            'read' => 1,
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
