<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\GroupLike;
use App\Models\GroupComment;
use App\Models\GroupRepost;

class GroupController extends Controller
{

    public function index() {
        $user = Auth::user();
    
        // Fetch all groups with their members, ordered by created_at in descending order
        // Exclude groups where the creator's 'deleted_at' is not null (archived users)
        $groups = Group::with('members')
            ->whereHas('creator', function ($query) {
                // Only include groups where the creator is not archived
                $query->whereNull('deleted_at');
            })
            ->select('name', 'description', 'thumbnail', 'slug', 'id')
            ->orderBy('created_at', 'desc')
            ->get();
    
        return view('groups', compact('user', 'groups'));
    }
    
    

    public function creategroup() 
    {

        $user = Auth::user();

        return view('groups.create', compact(

            'user'

        ));
    }
    // Create a new group

    public function store(Request $request)
        {
            $request->validate([
                'name' => 'required',
                'description' => 'required',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Add validation for image
            ]);

            $group = new Group;
            $group->name = $request->name;
            $group->description = $request->description;
            $group->user_id = auth()->id();
            $group->user_id = auth()->id();

            // Handle thumbnail upload
            if ($request->hasFile('thumbnail')) {
                $filePath = $request->file('thumbnail')->store('group-thumbnails', 'public');
                $group->thumbnail = $filePath;
            }

            $group->save();

            // Automatically add the creator as a member of the group
            $group->members()->attach(auth()->id());

            Alert::Success('Success, your group has been created successfully');
            return redirect()->route('groups.index')->with('success', 'Group created successfully');
        }

    // Join an existing group
    public function join(Group $group)
    {
        // Check if user is already a member
        if (!$group->members()->where('user_id', auth()->id())->exists()) {
            $group->members()->attach(auth()->id());
        }

        // Flash success message to session
        session()->flash('success', 'You have successfully joined the group!');

        return redirect()->route('groups.show', $group->slug);
    }
    

 // Show a group's details and posts
    public function show($slug)
    {
        $user = Auth::user();
        $group = Group::where('slug', $slug)
            ->with([
                'members' => function($query) {
                    $query->whereNull('deleted_at');
                },
                'posts' => function($query) {
                    $query->whereHas('user', function($query) {
                        $query->whereNull('deleted_at');
                    })
                    ->with(['comments' => function($query) {
                        $this->applyUserCondition($query);
                    }, 'reposts' => function($query) {
                        $this->applyUserCondition($query);
                    }]);
                },

                'posts.user',
                'posts.likes',
            ])
            ->firstOrFail();
    
        // Combine original posts with reposts
        $groupPosts = $group->posts->concat($group->posts->flatMap(function($post) {
            return $post->reposts;
        }));
    
        return view('groups.show', compact('group', 'user', 'groupPosts'));
    }

    protected function applyUserCondition($query) 
        {
            $query->whereHas('user', function($query) {
                $query->whereNull('deleted_at');
            });
        }
   

    // Engagement Metrics
    public function storeComment(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        GroupComment::create([
            'post_id' => $postId,
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return back()->with('success', 'Comment posted successfully');
    }

        public function likePost($postId)
    {
        GroupLike::create([
            'post_id' => $postId,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Post liked');
    }

    public function repostPost(Request $request, $postId)
    {
        $request->validate([
            'content' => 'nullable|string|max:1000',
        ]);

        GroupRepost::create([
            'post_id' => $postId,
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return back()->with('success', 'Post reposted');
    }

    public function myGroups()
    {
        $user = auth()->user();
        $myGroups = $user->groups()->get();

        return view('groups.my-groups', compact('myGroups'));
    }

    // Show the form for editing the group
    public function edit($id)
    {
        $group = Group::findOrFail($id);

        // Ensure the user editing the group is the owner of the group
        if (auth()->user()->id !== $group->user_id) {
            return redirect()->route('my-groups')->with('error', 'You are not authorized to edit this group.');
        }

        return view('groups.edit', compact('group'));
    }

    // Update the group
    public function update(Request $request, $slug)
    {
        $group = Group::where('slug', $slug)->firstOrFail();
    
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048'
        ]);
    
        // Update group details
        $group->name = $request->input('name');
        $group->description = $request->input('description');
    
        // Check if thumbnail is uploaded and store it
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('group-thumbnails', 'public');
            $group->thumbnail = $thumbnailPath;
        }
    
        $group->save();
    
        // Return back to the same edit page with a success message
        Alert::Success('Success, your group has been updated');
        return back()->with('success', 'Group updated successfully.');
    }    

    // Delete the group
    public function destroy($slug)
    {
        $group = Group::where('slug', $slug)->firstOrFail();
        
        // Delete the group
        $group->delete();
    
        // Return back to the same page with a success message after deletion
        return back()->with('success', 'Group deleted successfully.');
    }


    
}

