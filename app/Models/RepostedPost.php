<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepostedPost extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'original_post_id', 'created_at'];

     // Define the relationship to the original post
     public function originalPost()
     {
         return $this->belongsTo(Post::class, 'original_post_id');
     }

     public function reposter()
     {
         return $this->belongsTo(User::class, 'user_id');
     }
}

