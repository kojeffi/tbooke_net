<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\StudentDetail;
use App\Models\TeacherDetail;
use App\Models\AdminMessage;
use App\Models\AdminReply;

class SubjectController extends Controller
{
    public function userSubjects() 
    {
        try {
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

            // Fetch initial messages and unread counts
            $unreadMessagesCount = AdminMessage::where('is_read', false)->count();
            $unreadRepliesCount = AdminReply::where('admin_id', null)->where('is_read', false)->count();
            $totalUnreadCount = $unreadMessagesCount + $unreadRepliesCount;

            return response()->json([
                'subjects' => $subjects,
                'chosenSubjectsPercentage' => $chosenSubjectsPercentage,
                'mostPopularSubjectName' => $mostPopularSubjectName,
                'mostPopularSubjectCount' => $mostPopularSubjectCount,
                'totalUnreadCount' => $totalUnreadCount,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Could not fetch subjects. Please try again later.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $subject = Subject::create([
                'name' => $request->name,
            ]);

            return response()->json([
                'success' => 'Subject has been added successfully',
                'subject' => $subject,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Could not add subject. Please try again later.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $subject = Subject::findOrFail($id);
            $subject->update([
                'name' => $request->name,
            ]);

            return response()->json([
                'success' => 'Subject has been updated successfully',
                'subject' => $subject,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Could not update subject. Please try again later.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $subject = Subject::findOrFail($id);
            $subject->delete();

            return response()->json([
                'success' => 'Subject deleted successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Could not delete subject. Please try again later.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
