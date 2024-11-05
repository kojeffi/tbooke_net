<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\AdminMessage;
use App\Models\AdminReply;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function showLoginForm()
    {
        return response()->json(['message' => 'Admin login form'], 200);
    }

    public function login(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            return response()->json(['message' => 'Login successful', 'redirect' => route('admin.admin-panel')], 200);
        }

        return response()->json(['error' => 'Invalid credentials.'], 401);
    }

    public function index()
    {
        try {
            $users = User::with('institutionDetails')->paginate(6);

            $totalUsers = User::count();
            $newUsersThisMonth = User::whereMonth('created_at', now()->month)->count();
            $totalStudents = User::where('profile_type', 'student')->count();
            $totalTeachers = User::where('profile_type', 'teacher')->count();
            $totalInstitutions = User::where('profile_type', 'institution')->count();
            $totalOthers = User::where('profile_type', 'other')->count();
            $totalActiveUsers = User::where('last_login_at', '>=', Carbon::now()->subDays(30))->count();
            $totalInactiveUsers = User::where('last_login_at', '<', Carbon::now()->subDays(30))->orWhereNull('last_login_at')->count();

            $initialMessages = AdminMessage::with('replies')->orderBy('created_at', 'desc')->get();

            $unreadMessagesCount = AdminMessage::where('is_read', false)->count();
            $unreadRepliesCount = AdminReply::where('admin_id', null)->where('is_read', false)->count();
            $totalUnreadCount = $unreadMessagesCount + $unreadRepliesCount;

            $userCountPerMonth = User::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->groupBy('month')
                ->pluck('count', 'month')
                ->toArray();

            $userCountPerMonth = array_replace(array_fill(1, 12, 0), $userCountPerMonth);

            return response()->json([
                'users' => $users,
                'userCountPerMonth' => $userCountPerMonth,
                'initialMessages' => $initialMessages,
                'totalUnreadCount' => $totalUnreadCount,
                'totalUsers' => $totalUsers,
                'newUsersThisMonth' => $newUsersThisMonth,
                'totalStudents' => $totalStudents,
                'totalTeachers' => $totalTeachers,
                'totalInstitutions' => $totalInstitutions,
                'totalOthers' => $totalOthers,
                'totalActiveUsers' => $totalActiveUsers,
                'totalInactiveUsers' => $totalInactiveUsers,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve data.', 'message' => $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logout successful.'], 200);
    }
}
