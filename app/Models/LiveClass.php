<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;



class LiveClass extends Model
{
    protected $fillable = [
        'class_name',
        'class_category',
        'class_date',
        'class_time',
        'class_description',
        'registration_count',
        'creator_name',
        'creator_email',
        'user_id',
        'slug',
        'duration',
        'video_room_name',
    ];

    protected $casts = [
        'duration' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($liveClass) {
            $liveClass->slug = Str::slug($liveClass->class_name);

            if (empty($liveClass->duration)) {
                $liveClass->duration = 2; // Default duration of 120 minutes
            }
        });
    }

    // Define relationship with User
    public function users()
    {
        return $this->belongsToMany(User::class, 'live_class_user');
    }

    public function hasStarted()
    {
        $now = Carbon::now('Africa/Nairobi');
        $startDateTime = Carbon::parse("{$this->class_date} {$this->class_time}", 'Africa/Nairobi');
        return $now->gte($startDateTime);
    }

    public function isOngoing()
    {
        $now = Carbon::now('Africa/Nairobi');
        $startDateTime = Carbon::parse("{$this->class_date} {$this->class_time}", 'Africa/Nairobi');
        $endDateTime = $startDateTime->copy()->addHours($this->duration);
        return $now->between($startDateTime, $endDateTime);
    }
    

    public function hasEnded()
    {
        $now = Carbon::now('Africa/Nairobi');
        $startDateTime = Carbon::parse("{$this->class_date} {$this->class_time}", 'Africa/Nairobi');
        $endDateTime = $startDateTime->copy()->addHours($this->duration);
        return $now->gte($endDateTime);
    }

    // In LiveClass model
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
