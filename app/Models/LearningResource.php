<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LearningResource extends Model
{
    use HasFactory;

    // Add the fillable property to specify which attributes should be mass assignable
    protected $fillable = [
       'item_name',
        'county',
        'item_category',
        'description',
        'whatsapp_number',
        'contact_email',
        'contact_phone',
        'item_thumbnail',
        'item_price',
        'user_id'
    ];

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

        public function seller()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function boot (){
        parent::boot();

        static::creating(function($resource){
            $resource->slug = Str::slug($resource->item_name) . '-' . Str::random(6);
        });

    }

}
