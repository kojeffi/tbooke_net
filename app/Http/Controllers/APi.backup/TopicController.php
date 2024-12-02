<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\StudentDetail;
use App\Models\TeacherDetail;
use App\Models\AdminMessage;
use App\Models\AdminReply;
use Exception;

class TopicController extends Controller
{
    // Fetch and return all topics and related data
    public function index()
    {
        try {
            // Fetch all topics
            $topics = Topic::all();

            // Fetch all users
            $users = User::all();

            // Fetch user topics from student_details and teacher_details
            $studentTopics = StudentDetail::pluck('favorite_topics');
            $teacherTopics = TeacherDetail::pluck('favorite_topics');

            // Process and combine topics
            $studentTopics = $studentTopics->filter()->map(function ($item) {
                return explode(',', $item);
            })->flatten();

            $teacherTopics = $teacherTopics->filter()->map(function ($item) {
                return explode(',', $item);
            })->flatten();

            // Combine topics from both tables
            $allUserTopics = $studentTopics->merge($teacherTopics)->filter();

            // Count occurrences to find the most popular topic
            $topicCounts = $allUserTopics->countBy();

            // Calculate statistics
            $uniqueChosenTopicsCount = $topicCounts->count();
            $totalTopics = $topics->count();
            $totalTopicSelections = $allUserTopics->count();
            $chosenTopicsPercentage = ($totalTopics > 0) ? ($totalTopicSelections / ($totalTopics * $users->count())) * 100 : 0;
            $mostPopularTopicName = $topicCounts->keys()->first();
            $mostPopularTopicCount = $topicCounts->first();

            // Fetch initial admin messages and unread counts
            $initialMessages = AdminMessage::with('replies')
                ->orderBy('created_at', 'desc')
                ->get();

            $unreadMessagesCount = AdminMessage::where('is_read', false)->count();
            $unreadRepliesCount = AdminReply::where('admin_id', null)->where('is_read', false)->count();
            $totalUnreadCount = $unreadMessagesCount + $unreadRepliesCount;

            // Return a JSON response
            return response()->json([
                'topics' => $topics,
                'chosenTopicsPercentage' => $chosenTopicsPercentage,
                'mostPopularTopicName' => $mostPopularTopicName,
                'mostPopularTopicCount' => $mostPopularTopicCount,
                'unreadMessagesCount' => $unreadMessagesCount,
                'totalUnreadCount' => $totalUnreadCount,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while retrieving topics.'], 500);
        }
    }

    // Store a new topic
    public function store(Request $request)
    {
        try {
            // Validate incoming request
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            // Create a new topic
            $topic = Topic::create([
                'name' => $request->name,
            ]);

            // Return success response
            return response()->json([
                'message' => 'Topic has been added successfully',
                'topic' => $topic,
            ], 201); // 201 Created

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422); // 422 Unprocessable Entity
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while adding the topic.'], 500);
        }
    }

    // Update an existing topic
    public function update(Request $request, $id)
    {
        try {
            // Validate incoming request
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            // Find the topic or fail
            $topic = Topic::findOrFail($id);
            $topic->update([
                'name' => $request->name,
            ]);

            // Return success response
            return response()->json([
                'message' => 'Topic has been updated successfully',
                'topic' => $topic,
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422); // 422 Unprocessable Entity
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while updating the topic.'], 500);
        }
    }

    // Delete a topic
    public function destroy($id)
    {
        try {
            // Find the topic or fail
            $topic = Topic::findOrFail($id);
            $topic->delete();

            // Return success response
            return response()->json(['message' => 'Topic deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while deleting the topic.'], 500);
        }
    }
}
