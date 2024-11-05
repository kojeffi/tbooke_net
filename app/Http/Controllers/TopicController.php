<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\User;
use App\Models\StudentDetail;
use App\Models\TeacherDetail;
use App\Models\AdminMessage;
use App\Models\AdminReply;

class TopicController extends Controller
{
    public function index()
    {
        // Fetch all topics
        $topics = Topic::all();

        // Fetch all users
        $users = User::all();

        // Fetch user topics from student_details and teacher_details
        $studentTopics = StudentDetail::pluck('favorite_topics');
        $teacherTopics = TeacherDetail::pluck('favorite_topics');

        // Process and combine topics
        $studentTopics = $studentTopics->filter()->map(function($item) {
            return explode(',', $item);
        })->flatten();

        $teacherTopics = $teacherTopics->filter()->map(function($item) {
            return explode(',', $item);
        })->flatten();

        // Combine topics from both tables
        $allUserTopics = $studentTopics->merge($teacherTopics)->filter();

        // Count occurrences to find the most popular topic
        $topicCounts = $allUserTopics->countBy();

        // Calculate the total number of unique topics chosen
        $uniqueChosenTopicsCount = $topicCounts->count();

        // Calculate the total number of topics
        $totalTopics = $topics->count();

        // Calculate the number of total selections
        $totalTopicSelections = $allUserTopics->count();

        // Calculate the percentage of total selections over total possible selections
        $chosenTopicsPercentage = ($totalTopics > 0) ? ($totalTopicSelections / ($totalTopics * $users->count())) * 100 : 0;

        // Find the most popular topic
        $mostPopularTopicName = $topicCounts->keys()->first();
        $mostPopularTopicCount = $topicCounts->first();

        $initialMessages = AdminMessage::with('replies') 
        ->orderBy('created_at', 'desc') 
        ->get();

        // Calculate the unread count for the initial message and its replies - for the sidebar list
        $unreadMessagesCount = AdminMessage::where('is_read', false)->count();
        $unreadRepliesCount = AdminReply::where('admin_id', null)->where('is_read', false)->count();
        $totalUnreadCount = $unreadMessagesCount + $unreadRepliesCount;

        return view('admin-panel.user-topics', compact(
            'topics',
            'chosenTopicsPercentage',
            'mostPopularTopicName',
            'mostPopularTopicCount',
            'unreadMessagesCount',
            'totalUnreadCount',
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Topic::create([
            'name' => $request->name,
        ]);

        Alert::success('Topic has been added successfully');
        return redirect()->route('topics.index')->with('success', 'Topic added successfully');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $topic = Topic::findOrFail($id);
        $topic->update([
            'name' => $request->name,
        ]);

        Alert::success('Topic has been updated successfully');
        return redirect()->route('topics.index')->with('success', 'Topic updated successfully');
    }

    public function destroy($id)
    {
        $topic = Topic::findOrFail($id);
        $topic->delete();

        return redirect()->route('topics.index')->with('success', 'Topic deleted successfully');
    }
}

