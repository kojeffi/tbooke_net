<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\LearningResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Str;

class LearningResourceController extends Controller
{
    // Fetching tbooke resources for the authenticated user
    public function tbookeResources(): JsonResponse
    {
        $user = Auth::user();

        $resources = LearningResource::orderBy('created_at', 'desc')
            ->whereHas('user', function($query) {
                $query->whereNull('deleted_at');
            })
            ->get();

        return response()->json([
            'user' => $user,
            'resources' => $resources,
        ], 200);
    }

    // Creating tbooke resources - form (No view needed in API)
    public function index(): JsonResponse
    {
        $user = Auth::user();

        return response()->json([
            'user' => $user,
        ], 200);
    }

    // Storing tbooke resources
    public function store(Request $request): JsonResponse
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

            if (!$request->hasFile('item_thumbnail') && count($imagePaths) > 0) {
                $resource->item_thumbnail = $imagePaths[0];
            }
        }

        if ($request->hasFile('item_thumbnail')) {
            $thumbnail = $request->file('item_thumbnail');
            $thumbnailName = 'thumbnail_' . time() . '.' . $thumbnail->getClientOriginalExtension();
            $thumbnailPath = $thumbnail->storeAs('thumbnails', $thumbnailName, 'public');
            $resource->item_thumbnail = $thumbnailPath;
        }

        $resource->save();

        return response()->json([
            'message' => 'Item created successfully',
            'resource' => $resource
        ], 201);
    }

    // Fetching a specific resource by slug
    public function show($slug): JsonResponse
    {
        $resource = LearningResource::where('slug', $slug)
            ->whereHas('user', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->firstOrFail();

        $otherItems = LearningResource::where('slug', '!=', $slug)
            ->whereHas('user', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->take(7)
            ->get();

        return response()->json([
            'resource' => $resource,
            'otherItems' => $otherItems,
        ], 200);
    }

    // Fetching resources by user
    public function userResources($username): JsonResponse
    {
        $user = User::where('username', $username)->firstOrFail();

        $resources = LearningResource::where('user_id', $user->id)->latest()->get();

        return response()->json([
            'user' => $user,
            'resources' => $resources,
        ], 200);
    }

    // Editing a resource (No view needed in API)
    public function editResource($id): JsonResponse
    {
        $resource = LearningResource::where('id', $id)->firstOrFail();

        return response()->json([
            'resource' => $resource,
        ], 200);
    }

    // Updating a resource
    public function update(Request $request, $id): JsonResponse
    {
        $resource = LearningResource::findOrFail($id);

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

        $resource->update($validatedData);

        // Handle image updates
        if ($request->hasFile('item_images')) {
            $existingImages = $resource->item_images ? json_decode($resource->item_images, true) : [];
            $newImages = [];

            foreach ($request->file('item_images') as $file) {
                $fileName = 'item_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('learning-resources', $fileName, 'public');
                $newImages[] = $filePath;
            }

            $allImages = array_merge($existingImages, $newImages);
            $resource->item_images = json_encode($allImages);

            if (!$request->hasFile('item_thumbnail') && count($allImages) > 0) {
                $resource->item_thumbnail = $allImages[0];
            }
        }

        if ($request->hasFile('item_thumbnail')) {
            $thumbnail = $request->file('item_thumbnail');
            $thumbnailName = 'thumbnail_' . time() . '.' . $thumbnail->getClientOriginalExtension();
            $thumbnailPath = $thumbnail->storeAs('thumbnails', $thumbnailName, 'public');
            $resource->item_thumbnail = $thumbnailPath;
        }

        $resource->save();

        return response()->json([
            'message' => 'Item updated successfully',
            'resource' => $resource
        ], 200);
    }

    // Deleting a resource
    public function deleteResource($id): JsonResponse
    {
        $resource = LearningResource::findOrFail($id);
        $resource->delete();
        return response()->json([
            'message' => 'Item deleted successfully',
        ], 200);
    }
}
