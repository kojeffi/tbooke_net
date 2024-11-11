<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use App\Models\LiveClass;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Mail\ClassRegistrationNotification;
use App\Mail\ClassRegistrationConfirmation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class LiveClassController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'class_name' => 'required',
            'class_category' => 'required',
            'class_date' => 'required|date',
            'class_time' => 'required',
            'class_description' => 'required',
        ]);

        $user = Auth::user();

        // Create Metered meeting
        $METERED_DOMAIN = env('METERED_DOMAIN');
        $METERED_SECRET_KEY = env('METERED_SECRET_KEY');


        try {
            $response = Http::post("https://{$METERED_DOMAIN}/api/v1/room?secretKey={$METERED_SECRET_KEY}", [
                'autoJoin' => false,
                'showInviteBox' => false,
                'enableScreenSharing' => true,
                'appName' => 'Tbooke Live Classes',

            ]);

            if ($response->failed()) {
                return back()->with('error', 'Could not create Metered meeting. Please try again.');
            }

            $roomName = $response->json('roomName');

            // Validate and store live class data
            $liveClass = new LiveClass();
            $liveClass->class_name = $request->input('class_name');
            $liveClass->class_category = $request->input('class_category');
            $liveClass->class_date = $request->input('class_date');
            $liveClass->class_time = $request->input('class_time');
            $liveClass->class_description = $request->input('class_description');
            $liveClass->creator_name = $user->first_name . ' ' . $user->surname;
            $liveClass->creator_email = $user->email;
            $liveClass->slug = \Illuminate\Support\Str::slug($liveClass->class_name, '-');
            $liveClass->user_id = $user->id;
            $liveClass->duration = 2;
            $liveClass->video_room_name = $roomName;
            $liveClass->save();

            Alert::Success('Your class has been created successfully');
            return redirect()->route('tbooke-learning');

        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while creating the class. Please try again.');
        }
    }
    
    public function create()
    {
        $user = Auth::user();

        // Get notifications
        $notifications = Notification::with('sender')
            ->where('user_id', $user->id)
            ->where('type', 'New Connection')
            ->where('read', 0)
            ->orderByDesc('created_at')
            ->get();
        $notificationCount = $notifications->count();

        // Get message notifications
        $messagenotifications = Notification::with('sender')
            ->where('user_id', $user->id)
            ->where('type', 'New Message')
            ->where('read', 0)
            ->orderByDesc('created_at')
            ->get();
        $messagenotificationCount = $messagenotifications->count();

        return view('live-classes.create', [
            'user' => $user,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'messagenotifications' => $messagenotifications,
            'messagenotificationCount' => $messagenotificationCount,
        ]);
    }
    
    
    public function showLiveClass($slug)
{
    $liveClass = LiveClass::where('slug', $slug)->firstOrFail();
    $user = Auth::user();

            // Get notifications
            $notifications = Notification::with('sender')
            ->where('user_id', $user->id)
            ->where('type', 'New Connection')
            ->where('read', 0)
            ->orderByDesc('created_at')
            ->get();
        $notificationCount = $notifications->count();

        // Get message notifications
        $messagenotifications = Notification::with('sender')
            ->where('user_id', $user->id)
            ->where('type', 'New Message')
            ->where('read', 0)
            ->orderByDesc('created_at')
            ->get();
        $messagenotificationCount = $messagenotifications->count();

    return view('tbooke-learning.show-live-class', [
        'user' => $user,
        'notifications' => $notifications,
        'notificationCount' => $notificationCount,
        'messagenotifications' => $messagenotifications,
        'messagenotificationCount' => $messagenotificationCount,
        'liveClass' => $liveClass,
    ]);
}



    public function register(Request $request, $id)
    {
        $liveClass = LiveClass::findOrFail($id);
        $user = Auth::user();
    
        // Check if user is already registered
        if (!$liveClass->users()->where('user_id', $user->id)->exists()) {
            // Register user for the live class
            $liveClass->users()->attach($user->id);
    
            // Increment registration_count
            $liveClass->increment('registration_count');
    
            // Send confirmation email to the student
            Mail::to($user->email)->send(new ClassRegistrationConfirmation($liveClass, $user));
    
            // Send notification email to the creator using the creator_email field
            if ($liveClass->creator_email) {
                Mail::to($liveClass->creator_email)->send(new ClassRegistrationNotification($liveClass, $user));
            } else {
                Log::error("Live class {$liveClass->id} does not have a valid creator email.");
            }
    
            Alert::success('You have registered for the class successfully');
            return redirect()->route('tbooke-live-classes.index');

    }

        // User is already registered
        return redirect()->route('tbooke-live-classes.index')->with('warning', 'You are already registered for this class.');
    }

    public function creatorClasses()
    {
        $user = Auth::user();
    
        // Fetch live classes created by the logged-in user and sort them
        $creatorClasses = LiveClass::where('user_id', $user->id)
            ->get()
            ->sort(function ($a, $b) {
                if ($a->hasEnded() === $b->hasEnded()) {
                    $aStart = Carbon::parse("{$a->class_date} {$a->class_time}");
                    $bStart = Carbon::parse("{$b->class_date} {$b->class_time}");
                    return $aStart->lt($bStart) ? -1 : 1;
                }
                return $a->hasEnded() ? 1 : -1;
            });
    
        // Fetch live classes created by other users who are not archived
        $otherClasses = LiveClass::where('user_id', '!=', $user->id)
            ->whereHas('user', function ($query) {
                $query->whereNull('deleted_at'); 
            })
            ->get()
            ->sort(function ($a, $b) {
                if ($a->hasEnded() === $b->hasEnded()) {
                    $aStart = Carbon::parse("{$a->class_date} {$a->class_time}");
                    $bStart = Carbon::parse("{$b->class_date} {$b->class_time}");
                    return $aStart->lt($bStart) ? -1 : 1;
                }
                return $a->hasEnded() ? 1 : -1;
            });
    
        return view('live-classes', [
            'user' => $user,
            'creatorClasses' => $creatorClasses, 
            'otherClasses' => $otherClasses,
        ]);
    }
       


public function edit($id)
{
    $liveClass = LiveClass::findOrFail($id);
    $user = Auth::user();

    // Get notifications (if needed for the view)
    $notifications = Notification::with('sender')
        ->where('user_id', $user->id)
        ->where('type', 'New Connection')
        ->where('read', 0)
        ->orderByDesc('created_at')
        ->get();
    $notificationCount = $notifications->count();

    // Get message notifications (if needed for the view)
    $messagenotifications = Notification::with('sender')
        ->where('user_id', $user->id)
        ->where('type', 'New Message')
        ->where('read', 0)
        ->orderByDesc('created_at')
        ->get();
    $messagenotificationCount = $messagenotifications->count();

    return view('live-classes.edit', [
        'user' => $user,
        'notifications' => $notifications,
        'notificationCount' => $notificationCount,
        'messagenotifications' => $messagenotifications,
        'messagenotificationCount' => $messagenotificationCount,
        'liveClass' => $liveClass,
    ]);
}

public function update(Request $request, $id)
{
    $request->validate([
        'class_name' => 'required',
        'class_category' => 'required',
        'class_date' => 'required|date',
        'class_time' => 'required',
        'class_description' => 'required',
    ]);

    $liveClass = LiveClass::findOrFail($id);
    $liveClass->class_name = $request->input('class_name');
    $liveClass->class_category = $request->input('class_category');
    $liveClass->class_date = $request->input('class_date');
    $liveClass->class_time = $request->input('class_time');
    $liveClass->class_description = $request->input('class_description');
    $liveClass->save();

    Alert::success('Success', 'Live class updated successfully');
    return redirect()->route('live-classes.index');
}

public function destroy($id)
{
    $liveClass = LiveClass::findOrFail($id);
    $liveClass->delete();

    Alert::success('Success', 'Live class deleted successfully');
    return redirect()->route('live-classes.index');
}



}
         

