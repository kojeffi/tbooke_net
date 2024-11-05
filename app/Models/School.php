<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class School extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'thumbnail', 'user_id'];

      // Automatically generate a slug when saving the model
      protected static function boot()
      {
          parent::boot();
  
          static::saving(function ($model) {
              $model->slug = Str::slug($model->name);
          });
      }

      public function user()
      {
          return $this->belongsTo(User::class);
      }
}

