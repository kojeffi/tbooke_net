<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\User;
use App\Models\StudentDetail;
use App\Models\TeacherDetail;
use App\Models\AdminMessage;
use App\Models\AdminReply;

class SubjectController extends Controller
{
    public function userSubjects() 
    {
        // Fetch all subjects
        $subjects = Subject::all();

        // Fetch all users
        $users = User::all();

        // Fetch user subjects from student_details and teacher_details
        $studentSubjects = StudentDetail::pluck('user_subjects');
        $teacherSubjects = TeacherDetail::pluck('user_subjects');

        // Process and combine subjects
        $studentSubjects = $studentSubjects->filter()->map(function($item) {
            return explode(',', $item);
        })->flatten();

        $teacherSubjects = $teacherSubjects->filter()->map(function($item) {
            return explode(',', $item);
        })->flatten();

        // Combine subjects from both tables
        $allUserSubjects = $studentSubjects->merge($teacherSubjects)->filter();

        // Count occurrences to find the most popular subject
        $subjectCounts = $allUserSubjects->countBy();

        // Calculate the total number of unique subjects chosen
        $uniqueChosenSubjectsCount = $subjectCounts->count();

        // Calculate the total number of subjects
        $totalSubjects = $subjects->count();

        // Calculate the number of total selections
        $totalSelections = $allUserSubjects->count();

        // Calculate the percentage of total selections over total possible selections
        $chosenSubjectsPercentage = ($totalSubjects > 0) ? ($totalSelections / ($totalSubjects * $users->count())) * 100 : 0;

        // Find the most popular subject
        $mostPopularSubjectName = $subjectCounts->keys()->first();
        $mostPopularSubjectCount = $subjectCounts->first();

        $initialMessages = AdminMessage::with('replies') 
        ->orderBy('created_at', 'desc') 
        ->get();

        // Calculate the unread count for the initial message and its replies - for the sidebar list
        $unreadMessagesCount = AdminMessage::where('is_read', false)->count();
        $unreadRepliesCount = AdminReply::where('admin_id', null)->where('is_read', false)->count();
        $totalUnreadCount = $unreadMessagesCount + $unreadRepliesCount;

        return view('admin-panel.user-subjects', compact('subjects', 'chosenSubjectsPercentage', 'mostPopularSubjectName', 'mostPopularSubjectCount', 'totalUnreadCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Subject::create([
            'name' => $request->name,
        ]);

        Alert::Success('Subject has been added successfully');
        return redirect()->route('user.subjects')->with('success', 'Subject added successfully');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $subject = Subject::findOrFail($id);
        $subject->update([
            'name' => $request->name,
        ]);

        Alert::Success('Subject has been updated successfully');
        return redirect()->route('user.subjects')->with('success', 'Subject updated successfully');
    }

    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();

        return redirect()->route('user.subjects')->with('success', 'Subject deleted successfully');
    }
}

