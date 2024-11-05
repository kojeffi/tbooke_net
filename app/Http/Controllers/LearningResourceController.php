<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LearningResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Models\Notification;
use App\Models\User;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;

class LearningResourceController extends Controller
{
    //displaying tbooke resources
    public function tbookeResources()
    {
        $user = Auth::user();

        $resources = LearningResource::orderBy('created_at', 'desc')
        ->whereHas('user', function($query) {
            $query->whereNull('deleted_at');
        })
        ->get();

        return view('learning-resources', [
            'user' => $user,
            'resources' => $resources,
        ]);
      
    }

    //creating tbooke resources - form
    public function index() {
        
        $user = Auth::user();

        return view('learning-resources.create', [
            'user' => $user,
        ]);
    }

     //storing tbooke resources
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'item_name' => 'required|string',
            'county' => 'required|string',
            'item_category' => 'required|string',
            'item_price' => 'required|numeric|min:0',
            'contact_phone' => 'required|string',
            'contact_email' => 'required|string|email',
            'whatsapp_number' => 'required|string',
            'item_images' => 'required|array', 
            'item_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', 
            'item_thumbnail' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:2048',
            'description' => 'required|string'
        ]);
    
        // Create a new resource instance and fill it with validated data
        $resource = new LearningResource();
        $resource->item_name = $validatedData['item_name'];
        $resource->county = $validatedData['county'];
        $resource->item_category = $validatedData['item_category'];
        $resource->item_price = $validatedData['item_price'];
        $resource->contact_phone = $validatedData['contact_phone'];
        $resource->contact_email = $validatedData['contact_email'];
        $resource->whatsapp_number = $validatedData['whatsapp_number'];
        $resource->description = $validatedData['description'];
        $resource->user_id = auth()->id(); 
        $resource->slug = Str::slug($resource->item_name) . '-' . uniqid();

        // Handle file uploads for item_images
        if ($request->hasFile('item_images')) {
            $imagePaths = [];

            foreach ($request->file('item_images') as $file) {
                $fileName = 'item_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('learning-resources', $fileName, 'public');
                $imagePaths[] = $filePath;
            }

            $resource->item_images = json_encode($imagePaths); // Store all images as JSON

            // If no item_thumbnail provided, use the first item image as the thumbnail
            if (!$request->hasFile('item_thumbnail') && count($imagePaths) > 0) {
                $resource->item_thumbnail = $imagePaths[0];
            }
        }

        // Handle optional item_thumbnail if provided
        if ($request->hasFile('item_thumbnail')) {
            $thumbnail = $request->file('item_thumbnail');
            $thumbnailName = 'thumbnail_' . time() . '.' . $thumbnail->getClientOriginalExtension();
            $thumbnailPath = $thumbnail->storeAs('thumbnails', $thumbnailName, 'public');
            $resource->item_thumbnail = $thumbnailPath;
        }

        // Save the resource to the database
        $resource->save();

        Alert::Success('Your Item has been added successfully');
        return redirect()->route('learning-resources');
    }


    public function show ($slug) {
        $user = Auth::user();

        $otherItems = LearningResource::where('slug', '!=', $slug)
        ->whereHas('user', function($query){
            $query->whereNull('deleted_at');
        })
        ->take(7)
        ->get();

        $resource = LearningResource::where('slug', $slug)
        ->whereHas('user', function($query) {
            $query->whereNull('deleted_at');
        })
        ->first();
        
        return view('learning-resources.show', compact('resource', 'otherItems'));
    }
    

    public function userResources ($username) {

        $user = User::where('username', $username)->firstOrFail();

        $resources = LearningResource::where('user_id', $user->id)->latest()->get();

        return view('learning-resources.user-items', [
            'user' => $user,
            'resources' => $resources,
        ]);
    }
    

    public function editResource ($id) {

        $resource = LearningResource::where('id', $id)->firstOrFail();
        $user = Auth::user();

        return view('learning-resources.edit', compact('resource', 'user'));
    }

    public function update(Request $request, $id)
    {

         // Find the resource by ID
         $resource = LearningResource::findOrFail($id);
         
        // Validate the request data
        $validatedData = $request->validate([
            'item_name' => 'required|string',
            'county' => 'required|string',
            'item_category' => 'required|string',
            'item_price' => 'required|numeric|min:0',
            'contact_phone' => 'required|string',
            'contact_email' => 'required|string|email',
            'whatsapp_number' => 'required|string',
            'description' => 'required|string',
            'item_images' => 'nullable|array', 
            'item_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', 
            'item_thumbnail' => 'nullable|image|mimes:jpeg,png,webp,jpg,gif|max:2048',
        ]);

       

        // Update resource attributes with validated data
        $resource->item_name = $validatedData['item_name'];
        $resource->county = $validatedData['county'];
        $resource->item_category = $validatedData['item_category'];
        $resource->item_price = $validatedData['item_price'];
        $resource->contact_phone = $validatedData['contact_phone'];
        $resource->contact_email = $validatedData['contact_email'];
        $resource->whatsapp_number = $validatedData['whatsapp_number'];
        $resource->description = $validatedData['description'];

        // Handle item images upload if provided
        if ($request->hasFile('item_images')) {
            // Retrieve existing images from the database and decode them if they exist
            $existingImages = $resource->item_images ? json_decode($resource->item_images, true) : [];

            $newImages = [];

            // Loop through each new file and upload it
            foreach ($request->file('item_images') as $file) {
                $fileName = 'item_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('learning-resources', $fileName, 'public');
                $newImages[] = $filePath; // Add each new file path to the array
            }

            // Merge existing images with newly uploaded ones
            $allImages = array_merge($existingImages, $newImages);

            // Store merged images as JSON in the database
            $resource->item_images = json_encode($allImages);

            // If no item_thumbnail provided, use the first image as the thumbnail
            if (!$request->hasFile('item_thumbnail') && count($allImages) > 0) {
                $resource->item_thumbnail = $allImages[0];
            }
        }


        // Handle optional item_thumbnail if provided
        if ($request->hasFile('item_thumbnail')) {
            $thumbnail = $request->file('item_thumbnail');
            $thumbnailName = 'thumbnail_' . time() . '.' . $thumbnail->getClientOriginalExtension();
            $thumbnailPath = $thumbnail->storeAs('thumbnails', $thumbnailName, 'public');
            $resource->item_thumbnail = $thumbnailPath;
        }

        // Save the updated resource
        $resource->save();
        Alert::Success('Success', 'Your item has been updated successfully');
        return back()->with('success', 'Content created successfully');
    }

    

    public function deleteResource($id)
    {
        $resource = LearningResource::find($id);
        
            $resource->delete();

            // Redirect with success message
            return redirect()->back()->with('success', 'Item deleted successfully.');
    }
    

}
