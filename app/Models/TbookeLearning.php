<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TbookeLearning extends Model
{
    protected $table = 'tbooke_learning';
    
    use HasFactory;

    protected $fillable = ['content_title', 'content_thumbnail', 'content_category', 'content', 'slug', 'media_files'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
