<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Creator;
use App\Models\TbookeLearning;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;
use App\Models\Notification;
use App\Models\LiveClass;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class TbookeLearningController extends Controller
{
    public function TbookeLearning(Request $request)
    {
        $user = Auth::user();
        $contents = TbookeLearning::with(['user' => function ($query) {
            $query->whereNull('deleted_at'); 
        }])
        ->whereHas('user', function ($query) {
            $query->whereNull('deleted_at');
        })
        ->latest()
        ->get();
        
        $liveClasses = LiveClass::latest()->get();
        
        return view('tbooke-learning', [
            'user' => $user,
            'contents' => $contents, 
            'liveClasses' => $liveClasses,
        ]);
    }    

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'content_title' => 'required|string|max:255',
            'content_category' => 'required|array',
            'content' => 'required|string',
            'media_files.*' => 'nullable|mimes:jpg,jpeg,png,bmp,gif,svg,pdf,doc,docx,ppt,pptx,mp4|max:10000',
        ]);
    
        // Handle thumbnail upload (if provided)
        $thumbnailPath = null; // Default to null if no thumbnail is uploaded
        if ($request->hasFile('content_thumbnail')) {
            $thumbnailFile = $request->file('content_thumbnail');
            $thumbnailFileName = 'content_' . uniqid() . '.' . $thumbnailFile->getClientOriginalExtension();
            $thumbnailPath = $thumbnailFile->storeAs('thumbnails', $thumbnailFileName, 'public');
        }
    
        // Convert array of categories to comma-separated string
        $contentCategory = implode(',', $validatedData['content_category']);
    
        // Generate slug from the content title
        $slug = Str::slug($validatedData['content_title']) . '-' . uniqid();
    
        // Handle media files upload
        $mediaFiles = [];
        if ($request->hasFile('media_files')) {
            foreach ($request->file('media_files') as $mediaFile) {
                // Generate a unique filename for each media file
                $mediaFileName = 'media_' . uniqid() . '_' . $mediaFile->getClientOriginalName();
                $mediaFilePath = $mediaFile->storeAs('media_files', $mediaFileName, 'public');
                $mediaFiles[] = $mediaFilePath;
            }
        }
    
        // Save content to the database
        $content = new TbookeLearning();
        $content->content_title = $validatedData['content_title'];
        $content->content_thumbnail = $thumbnailPath;
        $content->content_category = $contentCategory;
        $content->content = $validatedData['content'];
        $content->slug = $slug;
        $content->user_id = auth()->id();
        $content->media_files = json_encode($mediaFiles); // Save media file paths as JSON
        $content->save();
    
        Alert::Success('Your content has been added successfully');
        return redirect()->route('tbooke-learning');
    }
       
    
    
    public function index () {

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
       

        return view('tbooke-learning.create', [
            'user' => $user,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'messagenotifications' => $messagenotifications,
            'messagenotificationCount' => $messagenotificationCount,
        ]);
        
    }

    public function userContents($username)
    {
        // Find the user by username
        $user = User::where('username', $username)->firstOrFail();

        // Fetch the user's content
        $contents = TbookeLearning::where('user_id', $user->id)->latest()->get();
        return view('tbooke-learning.user-contents', compact('user', 'contents'));

    }

    public function editContent($username, $id)
    {
        // Find the content by id
        $content = TbookeLearning::where('id', $id)->firstOrFail();
        $user = Auth::user();
        if ($content->user->username !== $username) {
            abort(403, 'Unauthorized action.'); 
        }
        // Return the edit view with the content data
        return view('tbooke-learning.edit', compact('content', 'user', 'username'));
    }


    public function update(Request $request, $id)
    {
        // Find the content by id
        $content = TbookeLearning::where('id', $id)->firstOrFail();
    
        // Validate the request
        $validatedData = $request->validate([
            'content_title' => 'required|string',
            'content' => 'required|string',
            'content_category' => 'sometimes|array', // Ensure content_category is an array
            'content_category.*' => 'string', // Each category should be a string
            'media_files.*' => 'file|mimes:jpg,jpeg,png,mp4,mov', // Adjust file validation rules as needed
            'content_thumbnail' => 'sometimes|file|mimes:jpg,jpeg,png|max:2048', // Validate the thumbnail
        ]);
    
        // Update basic content fields
        $content->content_title = $validatedData['content_title'];
        $content->content = $validatedData['content'];
    
        // Update content categories
        if (isset($validatedData['content_category'])) {
            $existingCategories = $content->content_category ? explode(',', $content->content_category) : [];
            $newCategories = array_merge($existingCategories, $validatedData['content_category']);
            $content->content_category = implode(',', array_unique($newCategories)); // Avoid duplicates
        }
    
        // Handle media files upload
        $mediaFiles = json_decode($content->media_files) ?? []; // Get existing media files (if stored as JSON)
    
        if ($request->hasFile('media_files')) {
            foreach ($request->file('media_files') as $mediaFile) {
                $mediaFileName = 'media_' . time() . '_' . $mediaFile->getClientOriginalName();
                $mediaFilePath = $mediaFile->storeAs('media_files', $mediaFileName, 'public'); // Store in public/media_files
                $mediaFiles[] = $mediaFilePath; // Add new file path to existing media files
            }
        }
    
        // Store the updated list of media files in JSON format
        $content->media_files = json_encode($mediaFiles);
    
        // Handle content thumbnail upload
        if ($request->hasFile('content_thumbnail')) {
            // Delete the old thumbnail if it exists
            if ($content->content_thumbnail) {
                Storage::disk('public')->delete($content->content_thumbnail);
            }
    
            // Save new thumbnail using the original method
            $file = $request->file('content_thumbnail');
            $fileName = 'content_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('thumbnails', $fileName, 'public'); // Store in public/thumbnails
            $content->content_thumbnail = $filePath; // Update the thumbnail path
        }
    
        // Save the updated content
        $content->save();
    
        Alert::Success('Success, Your content has been updated successfully');
        return back()->with('success', 'Content created successfully');
    }
    
    

    public function deleteContent($username, $id)
    {
        // Find the content by id
        $content = TbookeLearning::where('id', $id)->firstOrFail();
        if ($content->user->username !== $username) {
            abort(403, 'Unauthorized action.'); 
        }
        $content->delete();

        // Redirect back with a success message
        return back()->with('success', 'Content created successfully');
    }


   
}
