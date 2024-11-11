<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherDetail extends Model
{
    protected $fillable = ['user_id','about', 'other_name', 'socials', 'website']; // Add other fillable fields as needed

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


