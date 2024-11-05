<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupPost extends Model
{
    use HasFactory;

    protected $fillable = ['group_id', 'user_id', 'content', 'media'];

    // A group post belongs to a group
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    // A group post belongs to a user (author)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

        // Relationships for comments, likes, and reposts

    public function comments()
    {
        return $this->hasMany(GroupComment::class, 'post_id');
    }
    
    public function likes()
    {
        return $this->hasMany(GroupLike::class, 'post_id');
    }
    
    public function reposts()
    {
        return $this->hasMany(GroupRepost::class, 'post_id');
    }  
      
}
