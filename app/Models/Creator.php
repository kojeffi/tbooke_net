<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Creator extends Model
{
    use HasFactory;

    protected $fillable = ['first_name','surname','creator_subjects', 'creator_expertise', 'the_why'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
