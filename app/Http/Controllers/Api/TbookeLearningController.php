<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Creator;
use App\Models\TbookeLearning;
use App\Models\Notification;
use App\Models\LiveClass;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TbookeLearningController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Fetch contents and live classes
        $contents = TbookeLearning::with(['user' => function ($query) {
            $query->whereNull('deleted_at');
        }])
        ->whereHas('user', function ($query) {
            $query->whereNull('deleted_at');
        })
        ->latest()
        ->get();

        $liveClasses = LiveClass::latest()->get();

        return response()->json([
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

        // Handle thumbnail upload
        $thumbnailPath = null;
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
        $content->media_files = json_encode($mediaFiles);
        $content->save();

        return response()->json(['message' => 'Your content has been added successfully'], 201);
    }

    public function userContents($username)
    {
        // Find the user by username
        $user = User::where('username', $username)->firstOrFail();

        // Fetch the user's content
        $contents = TbookeLearning::where('user_id', $user->id)->latest()->get();
        return response()->json([
            'user' => $user,
            'contents' => $contents,
        ]);
    }

    public function editContent($username, $id)
    {
        // Find the content by id
        $content = TbookeLearning::where('id', $id)->firstOrFail();
        if ($content->user->username !== $username) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }
        return response()->json($content);
    }

    public function update(Request $request, $id)
    {
        // Find the content by id
        $content = TbookeLearning::where('id', $id)->firstOrFail();

        // Validate the request
        $validatedData = $request->validate([
            'content_title' => 'required|string',
            'content' => 'required|string',
            'content_category' => 'sometimes|array',
            'media_files.*' => 'file|mimes:jpg,jpeg,png,mp4,mov',
            'content_thumbnail' => 'sometimes|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Update basic content fields
        $content->content_title = $validatedData['content_title'];
        $content->content = $validatedData['content'];

        // Update content categories
        if (isset($validatedData['content_category'])) {
            $existingCategories = $content->content_category ? explode(',', $content->content_category) : [];
            $newCategories = array_merge($existingCategories, $validatedData['content_category']);
            $content->content_category = implode(',', array_unique($newCategories));
        }

        // Handle media files upload
        $mediaFiles = json_decode($content->media_files) ?? [];
        if ($request->hasFile('media_files')) {
            foreach ($request->file('media_files') as $mediaFile) {
                $mediaFileName = 'media_' . time() . '_' . $mediaFile->getClientOriginalName();
                $mediaFilePath = $mediaFile->storeAs('media_files', $mediaFileName, 'public');
                $mediaFiles[] = $mediaFilePath;
            }
        }

        $content->media_files = json_encode($mediaFiles);

        // Handle content thumbnail upload
        if ($request->hasFile('content_thumbnail')) {
            if ($content->content_thumbnail) {
                Storage::disk('public')->delete($content->content_thumbnail);
            }

            $file = $request->file('content_thumbnail');
            $fileName = 'content_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('thumbnails', $fileName, 'public');
            $content->content_thumbnail = $filePath;
        }

        // Save the updated content
        $content->save();

        return response()->json(['message' => 'Your content has been updated successfully']);
    }

    public function deleteContent($username, $id)
    {
        // Find the content by id
        $content = TbookeLearning::where('id', $id)->firstOrFail();
        if ($content->user->username !== $username) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }
        $content->delete();

        return response()->json(['message' => 'Content deleted successfully']);
    }
}
