<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\AdminMessage;
use Illuminate\Support\Facades\DB;
use App\Models\AdminReply;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.admin-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->intended(route('admin.admin-panel'));
        }

        return redirect()->back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function index()
    {

        $totalUsers = User::count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)->count();

        $totalStudents = User::where('profile_type', 'student')->count();
        $totalTeachers = User::where('profile_type', 'teacher')->count();
        $totalInstitutions = User::where('profile_type', 'institution')->count();
        $totalOthers = User::where('profile_type', 'other')->count();

        // Users who logged in within the last 30 days
        $totalActiveUsers = User::where('last_login_at', '>=', Carbon::now()->subDays(30))->count();

        // Users who haven't logged in for more than 30 days
        $totalInactiveUsers = User::where('last_login_at', '<', Carbon::now()->subDays(30))->orWhereNull('last_login_at')->count();

        $initialMessages = AdminMessage::with('replies') 
        ->orderBy('created_at', 'desc') 
        ->get();

        // Calculate the unread count for the initial message and its replies - for the sidebar list
        $unreadMessagesCount = AdminMessage::where('is_read', false)->count();
        $unreadRepliesCount = AdminReply::where('admin_id', null)->where('is_read', false)->count();
        $totalUnreadCount = $unreadMessagesCount + $unreadRepliesCount;

        // Calculate user count per month
        $userCountPerMonth = User::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();
    
        // Ensure the array has 12 elements for each month
        $userCountPerMonth = array_replace(array_fill(1, 12, 0), $userCountPerMonth);

        // Add logic to fetch data or perform actions for the admin dashboard
        return view('admin-panel', compact('userCountPerMonth', 'initialMessages', 'totalUnreadCount', 'totalUsers', 'newUsersThisMonth', 'totalStudents', 'totalTeachers', 'totalInstitutions', 'totalOthers',  'totalActiveUsers',
        'totalInactiveUsers'));
    }

   

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout(); // Use the admin guard to logout                              
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login'); // Redirect to the admin login page after logout
    }

    public function showUsers() {
        // Retrieve all users, including soft-deleted (archived) ones
        $users = User::with('institutionDetails')->withTrashed()->paginate(70);
        
        // Calculate unread counts
        $unreadMessagesCount = AdminMessage::where('is_read', false)->count();
        $unreadRepliesCount = AdminReply::where('admin_id', null)->where('is_read', false)->count();
        $totalUnreadCount = $unreadMessagesCount + $unreadRepliesCount;
    
        return view('admin-panel.users', compact('users', 'totalUnreadCount'));
    }
    
    

    public function archive($id)
    {
        $user = User::findOrFail($id);

        // Soft delete the user
        $user->delete();

        return redirect()->route('admin-panel.users')->with('success', 'User archived successfully');
    }

    public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    // Validate the input data
    $request->validate([
        'first_name' => 'required|string|max:255',
        'surname' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email,'.$user->id,
    ]);

    // Update the user's information
    $user->update([
        'first_name' => $request->first_name,
        'surname' => $request->surname,
        'email' => $request->email,
    ]);

    return redirect()->route('admin-panel.users')->with('success', 'User updated successfully');
}

public function unarchiveUser($id)
{
    $user = User::withTrashed()->find($id);
    if ($user) {
        $user->deleted_at = null; // Set deleted_at back to null
        $user->save();
        return redirect()->back()->with('success', 'User unarchived successfully.');
    }
    return redirect()->back()->with('error', 'User not found.');
}




}
