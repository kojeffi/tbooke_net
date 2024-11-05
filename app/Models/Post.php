<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model

{
    
    protected $fillable = [
        'user_id', 'content', 'media_path', 'repost_count', 'is_repost', 'original_post_id', 'original_user_id'
    ];

    protected $casts = [
        'media_path' => 'json', // Cast media_path to JSON array
    ];


   public function user()
   {
        return $this->belongsTo(User::class)->whereNull('users.deleted_at');
   }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->orderByDesc('created_at');
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'likes');
    }

    public function reposter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function originalPost()
    {
        return $this->belongsTo(Post::class, 'original_post_id');
    }

    public function originalUser()
    {
        return $this->belongsTo(User::class, 'original_user_id');
    }

    
}
