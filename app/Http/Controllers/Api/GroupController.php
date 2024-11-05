<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\GroupLike;
use App\Models\GroupComment;
use App\Models\GroupRepost;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class GroupController extends Controller
{
    public function index() {
        $user = Auth::user();

        try {
            // Fetch all groups with their members, ordered by created_at in descending order
            $groups = Group::with('members')
                ->whereHas('creator', function ($query) {
                    $query->whereNull('deleted_at');
                })
                ->select('name', 'description', 'thumbnail', 'slug', 'id')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json(['user' => $user, 'groups' => $groups], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to fetch groups'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function createGroup() {
        $user = Auth::user();
        return response()->json(['user' => $user], Response::HTTP_OK);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        try {
            $group = new Group;
            $group->name = $request->name;
            $group->description = $request->description;
            $group->user_id = auth()->id();

            // Handle thumbnail upload
            if ($request->hasFile('thumbnail')) {
                $filePath = $request->file('thumbnail')->store('group-thumbnails', 'public');
                $group->thumbnail = $filePath;
            }

            $group->save();
            // Automatically add the creator as a member of the group
            $group->members()->attach(auth()->id());

            return response()->json(['message' => 'Group created successfully', 'group' => $group], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to create group'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function join(Group $group) {
        try {
            // Check if user is already a member
            if (!$group->members()->where('user_id', auth()->id())->exists()) {
                $group->members()->attach(auth()->id());
            }

            return response()->json(['message' => 'Successfully joined the group!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to join group'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($slug) {
        $user = Auth::user();

        try {
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

            return response()->json(['group' => $group, 'user' => $user, 'groupPosts' => $groupPosts], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Group not found'], Response::HTTP_NOT_FOUND);
        }
    }

    protected function applyUserCondition($query) {
        $query->whereHas('user', function($query) {
            $query->whereNull('deleted_at');
        });
    }

    public function storeComment(Request $request, $postId) {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        try {
            GroupComment::create([
                'post_id' => $postId,
                'user_id' => auth()->id(),
                'content' => $request->content,
            ]);

            return response()->json(['message' => 'Comment posted successfully'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to post comment'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function likePost($postId) {
        try {
            GroupLike::create([
                'post_id' => $postId,
                'user_id' => auth()->id(),
            ]);

            return response()->json(['message' => 'Post liked'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to like post'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function repostPost(Request $request, $postId) {
        $validator = Validator::make($request->all(), [
            'content' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        try {
            GroupRepost::create([
                'post_id' => $postId,
                'user_id' => auth()->id(),
                'content' => $request->content,
            ]);

            return response()->json(['message' => 'Post reposted'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to repost'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function myGroups() {
        $user = auth()->user();

        try {
            $myGroups = $user->groups()->get();
            return response()->json(['myGroups' => $myGroups], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to fetch your groups'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function edit($id) {
        try {
            $group = Group::findOrFail($id);

            // Ensure the user editing the group is the owner of the group
            if (auth()->user()->id !== $group->user_id) {
                return response()->json(['error' => 'Unauthorized to edit this group'], Response::HTTP_FORBIDDEN);
            }

            return response()->json(['group' => $group], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Group not found'], Response::HTTP_NOT_FOUND);
        }
    }

    public function update(Request $request, $slug) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        try {
            $group = Group::where('slug', $slug)->firstOrFail();

            // Update group details
            $group->name = $request->input('name');
            $group->description = $request->input('description');

            // Check if thumbnail is uploaded and store it
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('group-thumbnails', 'public');
                $group->thumbnail = $thumbnailPath;
            }

            $group->save();

            return response()->json(['message' => 'Group updated successfully', 'group' => $group], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to update group'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($slug) {
        try {
            $group = Group::where('slug', $slug)->firstOrFail();
            $group->delete();

            return response()->json(['message' => 'Group deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to delete group'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
