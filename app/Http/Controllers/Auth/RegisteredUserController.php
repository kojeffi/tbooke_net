<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\UsernameHelper;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TeacherDetail;
use App\Models\StudentDetail;
use App\Models\OtherDetail;
use App\Models\InstitutionDetail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'profile_type' => ['required', 'string', 'max:255'],
            'accept_privacy_policy' => 'accepted',
        ];

        if ($request->profile_type === 'institution') {
            $rules['institution_name'] = ['required', 'string', 'max:255'];
            $rules['institution_location'] = ['required', 'string', 'max:255'];
        } else {
            $rules['first_name'] = ['required', 'string', 'max:255'];
            $rules['surname'] = ['required', 'string', 'max:255'];
        }

        $customMessages = [
            'institution_name.required' => 'The institution name field is required.',
            'institution_location.required' => 'The institution location field is required.',
        ];

        $request->validate($rules, $customMessages);

        $randomNumber = mt_rand(100000, 999999);
        $username = UsernameHelper::generateUniqueUsername($request->first_name ?? 'institution', $request->surname ?? 'institution');
        $username = $username . '-' . $randomNumber;

        // Create the user
        $user = User::create([
            'first_name' => $request->first_name ?? 'institution',
            'surname' => $request->surname ?? 'institution',
            'email' => $request->email,
            'username' => $username,
            'profile_type' => $request->profile_type,
            'password' => Hash::make($request->password),
        ]);

        // Dispatch the verification event
        $user->sendEmailVerificationNotification();         

        // Assign role based on profile_type
        $user->assignRole($request->profile_type);

        // Save additional details based on profile_type
        if ($request->profile_type === 'teacher') {
            $teacherDetail = new TeacherDetail(['user_id' => $user->id]);
            $user->teacherDetails()->save($teacherDetail);
        } elseif ($request->profile_type === 'student') {
            $studentDetail = new StudentDetail(['user_id' => $user->id]);
            $user->studentDetails()->save($studentDetail);
        } elseif ($request->profile_type === 'institution') {
            $institutionDetail = new InstitutionDetail([
                'user_id' => $user->id,
                'institution_name' => $request->institution_name,
                'institution_location' => $request->institution_location,
            ]);
            $user->institutionDetails()->save($institutionDetail);
        } elseif ($request->profile_type === 'other') {
            $otherDetail = new OtherDetail(['user_id' => $user->id]);
            $user->otherDetails()->save($otherDetail);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice')->with('id', $user->id);
    }
}
