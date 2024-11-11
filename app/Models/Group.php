<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'thumbnail', 'user_id'];

    // A group has many members
    public function members() {
        return $this->belongsToMany(User::class, 'group_members');
    }

    // A group has many posts
    public function posts() {
        return $this->hasMany(GroupPost::class);
    }

    // A group belongs to a creator (user)
    public function creator() {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Automatically generate slug when creating the group
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($group) {
            // Automatically generate a unique slug by combining the group name and a unique identifier
            $group->slug = Str::slug($group->name . '-' . uniqid());
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


