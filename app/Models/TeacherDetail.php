<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'about',
        'user_subjects',
        'favorite_topics',
        'socials',
        'profile_pic',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
