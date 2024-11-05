<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\User;
use Google\Service\Resource;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Storage;

class SchoolController extends Controller
{

    public function SchoolsCorner(Request $request)
    {
        $user = Auth::user();

        // Fetch all schools
        $schools = School::orderBy('created_at', 'desc')
        ->whereHas('user', function($query){
            $query->whereNull('deleted_at');
        })
        ->get();
    
        return view('schools-corner', [
            'user' => $user,
            'schools' => $schools,
        ]);
    }  

    public function create()
    {

        $user = Auth::user();

             

        return view('schools-corner.create', [
            'user' => $user,
        ]);
    }

    public function store(Request $request)
    {
        $user_id = auth()->id(); 

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'user_id' => $user_id,
        ]);
    
        // Handle file upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('schools-corner', 'public');
            $validatedData['thumbnail'] = $imagePath; // Use the correct field name here
        } else {
            $validatedData['thumbnail'] = 'default-thumbnail.jpg'; // Set a default value if necessary
        }
    
        // Create the school
        School::create($validatedData);
    

        // Redirect to a success page or back to the form
        Alert::Success('Your School has been added successfully');
        return redirect()->route('schools-corner');
    }

        public function show(School $school)
    {
        $user = Auth::user();
    
                  // Get notifications
                  $notifications = Notification::with('sender')
                  ->where('user_id', auth()->user()->id)
                  ->where('type', 'New Connection')
                  ->where('read', 0)
                  ->orderByDesc('created_at')
                  ->get();
              $notificationCount = $notifications->count();
           
              // Get notifications
                  $messagenotifications = Notification::with('sender')
                  ->where('user_id', auth()->user()->id)
                  ->where('type', 'New Message')
                  ->where('read', 0)
                  ->orderByDesc('created_at')
                  ->get();
              $messagenotificationCount = $messagenotifications->count();



        return view('schools-corner.show', [
            'user' => $user,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'school' => $school,
            'messagenotifications' => $messagenotifications,
            'messagenotificationCount' => $messagenotificationCount,
        ]);
    }
    
    public function showUserSchools($username) {
        $user = User::where('username', $username)->firstOrFail();
        $schools = School::where('user_id', $user->id)->latest()->get();
        
        return view ('schools-corner.user-schools', [
            'user' => $user,
            'schools' => $schools,
        ]);
    }
    
    public function editSchool ($id) {

        $school = School::where('id', $id)->firstOrFail();
        $user = Auth::user();

        return view('schools-corner.edit', compact('school', 'user'));
    }

    public function update(Request $request, $id)
    {
       
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        
        $school = School::findOrFail($id);
    
        $school->name = $request->input('name');
        $school->description = $request->input('description');
    
    
        if ($request->hasFile('image')) {
            
            $path = $request->file('image')->store('schools-corner', 'public');
    
            // Delete the old image if it exists
            if ($school->thumbnail) {
                Storage::disk('public')->delete($school->thumbnail);
            }
    
            // Update the school's thumbnail field
            $school->thumbnail = $path;
        }
    
        // Save the updated school
        $school->save();
    
        Alert::success('Success', 'School updated successfully!');
        return back();
    }

    public function deleteSchool($id)
    {
    
        $school = School::findOrFail($id);
        if ($school->thumbnail) {
            Storage::disk('public')->delete($school->thumbnail);
        }
    
        $school->delete();
    
        // Show a success message
        Alert::success('Deleted', 'School has been deleted successfully!');
    
        // Redirect back
        return back();
    }
    
    

}
