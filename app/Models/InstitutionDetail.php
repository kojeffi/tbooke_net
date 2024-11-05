<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstitutionDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'institution_name',
        'institution_about',
        'institution_location',
        'institution_website',
        'favorite_topics',
        'user_topics',
        'socials',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
