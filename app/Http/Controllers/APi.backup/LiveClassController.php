<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use App\Models\LiveClass;
use Illuminate\Support\Facades\Http;
use App\Mail\ClassRegistrationNotification;
use App\Mail\ClassRegistrationConfirmation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class LiveClassController extends Controller
{
    public function store(Request $request)
    {
        // Validate incoming request
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
                return response()->json(['error' => 'Could not create Metered meeting. Please try again.'], 500);
            }

            $roomName = $response->json('roomName');

            // Store live class data
            $liveClass = LiveClass::create([
                'class_name' => $request->input('class_name'),
                'class_category' => $request->input('class_category'),
                'class_date' => $request->input('class_date'),
                'class_time' => $request->input('class_time'),
                'class_description' => $request->input('class_description'),
                'creator_name' => $user->first_name . ' ' . $user->surname,
                'creator_email' => $user->email,
                'slug' => \Illuminate\Support\Str::slug($request->input('class_name')),
                'user_id' => $user->id,
                'duration' => 2,
                'video_room_name' => $roomName,
            ]);

            return response()->json(['success' => 'Your class has been created successfully', 'live_class' => $liveClass], 201);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'An error occurred while creating the class. Please try again.'], 500);
        }
    }
    
    public function liveClasses()
    {
        $liveClasses = LiveClass::with('users')->get();

        // Custom sorting: first by whether the class has ended, then by start date and time
        $liveClasses = $liveClasses->sort(function ($a, $b) {
            return $a->hasEnded() === $b->hasEnded() 
                ? Carbon::parse($a->class_date . ' ' . $a->class_time)->lt(Carbon::parse($b->class_date . ' ' . $b->class_time)) ? -1 : 1 
                : ($a->hasEnded() ? 1 : -1);
        });

        return response()->json(['live_classes' => $liveClasses]);
    }

    public function showLiveClass($slug)
    {
        $liveClass = LiveClass::where('slug', $slug)->firstOrFail();
        return response()->json(['live_class' => $liveClass]);
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
            // Send confirmation email
            Mail::to($user->email)->send(new ClassRegistrationConfirmation($liveClass, $user));

            // Send notification email to the creator
            if ($liveClass->creator_email) {
                Mail::to($liveClass->creator_email)->send(new ClassRegistrationNotification($liveClass, $user));
            } else {
                Log::error("Live class {$liveClass->id} does not have a valid creator email.");
            }

            return response()->json(['success' => 'You have registered for the class successfully'], 201);
        }

        return response()->json(['error' => 'You are already registered for this class.'], 400);
    }

    public function creatorClasses()
    {
        $user = Auth::user();
        $creatorClasses = LiveClass::where('user_id', $user->id)->get()->sort(/* sorting logic */);
        $otherClasses = LiveClass::where('user_id', '!=', $user->id)->get()->sort(/* sorting logic */);

        return response()->json([
            'creator_classes' => $creatorClasses,
            'other_classes' => $otherClasses,
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
        $liveClass->update($request->only(['class_name', 'class_category', 'class_date', 'class_time', 'class_description']));

        return response()->json(['success' => 'Live class updated successfully']);
    }

    // Add any additional methods for delete, etc., with similar JSON response structure
}
