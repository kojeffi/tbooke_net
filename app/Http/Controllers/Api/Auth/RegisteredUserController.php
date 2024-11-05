<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\UsernameHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TeacherDetail;
use App\Models\StudentDetail;
use App\Models\OtherDetail;
use App\Models\InstitutionDetail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request) // Renamed from 'register' to 'store'
    {
        // Validate the incoming request with the defined rules
        try {
            $rules = [
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
                'profile_type' => ['required', 'string', 'max:255'],
            ];

            // Additional validation rules for institutions
            if ($request->profile_type === 'institution') {
                $rules['institution_name'] = ['required', 'string', 'max:255'];
                $rules['institution_location'] = ['required', 'string', 'max:255'];
            } else {
                $rules['first_name'] = ['required', 'string', 'max:255'];
                $rules['surname'] = ['required', 'string', 'max:255'];
            }

            // Validate the request with the defined rules
            $validatedData = $request->validate($rules);
        } catch (ValidationException $e) {
            // Return a response for validation errors
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        try {
            // Generate a unique username
            $randomNumber = mt_rand(100000, 999999);
            $username = UsernameHelper::generateUniqueUsername($request->first_name ?? 'institution', $request->surname ?? 'institution');
            $username = $username . '-' . $randomNumber;

            // Create the user in the database
            $user = User::create([
                'first_name' => $request->first_name ?? 'institution',
                'surname' => $request->surname ?? 'institution',
                'email' => $request->email,
                'username' => $username,
                'profile_type' => $request->profile_type,
                'password' => Hash::make($request->password),
            ]);

            // Send email verification notification
            $user->sendEmailVerificationNotification();

            // Assign role based on profile_type (ensure the role exists)
            $user->assignRole($request->profile_type);

            // Save additional details based on profile_type
            switch ($request->profile_type) {
                case 'teacher':
                    $teacherDetail = new TeacherDetail(['user_id' => $user->id]);
                    $user->teacherDetails()->save($teacherDetail);
                    break;

                case 'student':
                    $studentDetail = new StudentDetail(['user_id' => $user->id]);
                    $user->studentDetails()->save($studentDetail);
                    break;

                case 'institution':
                    $institutionDetail = new InstitutionDetail([
                        'user_id' => $user->id,
                        'institution_name' => $request->institution_name,
                        'institution_location' => $request->institution_location,
                    ]);
                    $user->institutionDetails()->save($institutionDetail);
                    break;

                case 'other':
                    $otherDetail = new OtherDetail(['user_id' => $user->id]);
                    $user->otherDetails()->save($otherDetail);
                    break;
            }

            // Dispatch the Registered event
            event(new Registered($user));

            // Generate a token for the user (ensure you have Sanctum installed and configured)
            $token = $user->createToken('auth_token')->plainTextToken;

            // Return success response with the token and user data
            return response()->json([
                'message' => 'Registration successful. Please verify your email.',
                'user' => $user,
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error during registration: ' . $e->getMessage());

            // Return a response for the error
            return response()->json([
                'message' => 'Registration failed. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
