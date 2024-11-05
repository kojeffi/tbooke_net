<?php

namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; // Import the HasRoles trait
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasRoles;
    use Notifiable;
    use SoftDeletes;
    use HasApiTokens;

    protected $dates = ['deleted_at'];
    
    protected $fillable = [
        'first_name','surname', 'email', 'username', 'password', 'profile_type', 'profile_picture',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function teacherDetails()
    {
        return $this->hasOne(TeacherDetail::class, 'id');
    }

    public function studentDetails()
    {
        return $this->hasOne(StudentDetail::class, 'id');
    }
    public function institutionDetails()
    {
        return $this->hasOne(InstitutionDetail::class, 'id');
    }
    public function otherDetails()
    {
        return $this->hasOne(OtherDetail::class, 'id');
    }
    public function posts()
    {
        return $this->hasMany(Post::class);
    }


    public function followings()
    {
        return $this->belongsToMany(User::class, 'follower_user', 'follower_id', 'user_id')->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follower_user', 'user_id', 'follower_id')->withTimestamps();
    }

    public function follows(User $user) {
        return $this->followings()->where('user_id', $user->id)->exists();
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function sentNotifications()
    {
        return $this->hasMany(Notification::class, 'sender_id');
    }

    public function likes()
    {
        return $this->belongsToMany(Post::class, 'likes');
    }

    public function blueboardPosts()
    {
        return $this->hasMany(BlueboardPost::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function messages()
    {
        return $this->sentMessages->merge($this->receivedMessages);
    }

    public function repost()
    {
        return $this->hasMany(RepostedPost::class);
    }

    public function liveClasses()
    {
        return $this->belongsToMany(LiveClass::class, 'live_class_user');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

     public function sentAdminMessages()
     {
         return $this->hasMany(AdminMessage::class, 'sender_id');
     }
     public function receivedAdminMessages()
     {
         return $this->hasMany(AdminMessage::class, 'receiver_id');
     }
     public function groups()
     {
         return $this->hasMany(Group::class);
     }
     public function schools()
    {
        return $this->hasMany(School::class);
    }

}