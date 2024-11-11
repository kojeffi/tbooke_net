<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Creator;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\User;
use App\Models\Notification;
use App\Models\Subject;
use App\Models\Topic;

class ProfileController extends Controller
{

    // Show the user's profile.
    public function showOwn()
    {
        $user = Auth::user();

        // Get the user's profile details based on their profile type
        $profileDetails = null;
        if ($user->profile_type === 'teacher') {
            $profileDetails = $user->teacherDetails;
        } elseif ($user->profile_type === 'student') {
            $profileDetails = $user->studentDetails;
        } elseif ($user->profile_type === 'institution') {
            $profileDetails = $user->institutionDetails;
        } elseif ($user->profile_type === 'other') {
            $profileDetails = $user->otherDetails;
        }

          // Convert socials string to array if it's stored as JSON in the database
          if ($profileDetails->socials) {
            $profileDetails->socials = json_decode($profileDetails->socials, true);
        }

// Get the user's posts
$posts = $user->posts()
    ->with([
        'comments' => function ($query) {
            // Exclude comments from users who have been deleted (archived)
            $query->whereHas('user', function ($query) {
                $query->whereNull('deleted_at');
            });
        },
        'reposter', // Include reposter info
        'originalUser' => function ($query) {
            // Exclude posts from deleted (archived) users
            $query->whereNull('deleted_at');
        },
        'originalPost' => function ($query) {
            // Ensure comments are excluded from deleted (archived) users on original posts
            $query->with([
                'comments' => function ($query) {
                    $query->whereHas('user', function ($query) {
                        $query->whereNull('deleted_at');
                    });
                }
            ]);
        }
    ])
    ->whereHas('user', function ($query) {
        $query->whereNull('deleted_at'); // Ensure that only posts from active (non-archived) users are fetched
    })
    ->where(function ($query) {
        $query->where('is_repost', false) // Regular post
            ->orWhere(function ($query) {
                $query->where('is_repost', true) // Repost case
                    ->whereHas('originalUser', function ($query) {
                        $query->whereNull('deleted_at'); // Only include reposts where the original user's deleted_at is null (non-archived)
                    });
            });
    })
    ->orderBy('created_at', 'desc')
    ->get();


        

        
        $userIsCreator = Creator::where('user_id', $user->id)->exists();
        $userIsTeacher = $user->profile_type === 'teacher';
        
        // Get followers count
        $followersCount = $user->followers()->count();


             // Get notifications
             $notifications = Notification::with('sender')
             ->where('user_id', auth()->user()->id)
             ->where('type', 'New Connection')
             ->where('read', 0)
             ->orderByDesc('created_at')
             ->get();
         $notificationCount = $notifications->count();
      
         // Get notifications
             $messagenotifications = Notification::with('sender')
             ->where('user_id', auth()->user()->id)
             ->where('type', 'New Message')
             ->where('read', 0)
             ->orderByDesc('created_at')
             ->get();
         $messagenotificationCount = $messagenotifications->count();

         $adminnotifications = Notification::with('sender')
         ->where('user_id', $user->id)
         ->where('type', 'New Admin Message')
         ->where('read', 0)
         ->orderByDesc('created_at')
         ->get();
  
         // Calculate the counts for each type of message
         $messagenotificationCount = $messagenotifications->count();
         $adminnotificationCount = $adminnotifications->count();
  
         // Total notification count
         $totalMessageNotificationCount = $messagenotificationCount + $adminnotificationCount;
        
        return view('profile', [
            'user' => $user,
            'profileDetails' => $profileDetails,
            'posts' => $posts,
            'userIsCreator' => $userIsCreator,
            'userIsTeacher' => $userIsTeacher,
            'followersCount' => $followersCount,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'messagenotifications' => $messagenotifications,
            'messagenotificationCount' => $messagenotificationCount,
            'adminnotifications' => $adminnotifications,
            'adminnotificationCount' => $adminnotificationCount,
            'totalMessageNotificationCount' => $totalMessageNotificationCount,

        ]);
    }

    // Show the edit profile form.
    public function edit()
    {
        $user = Auth::user();

        // Fetch all subjects
        $subjects = Subject::all();
        $topics = Topic::all();

        // Get the user's profile details based on their profile type
        $profileDetails = null;
        if ($user->profile_type === 'teacher') {
            $profileDetails = $user->teacherDetails;
        } elseif ($user->profile_type === 'student') {
            $profileDetails = $user->studentDetails;
        } elseif ($user->profile_type === 'institution') {
            $profileDetails = $user->institutionDetails;
        } elseif ($user->profile_type === 'other') {
            $profileDetails = $user->otherDetails;
        }

        // Convert socials string to array if it's stored as JSON in the database
        if ($profileDetails->socials) {
            $profileDetails->socials = json_decode($profileDetails->socials, true);
        }

     
      // Get favorite topics if available
      $favoriteTopics = [];
      if ($profileDetails && $profileDetails->favorite_topics) {
          $favoriteTopics = explode(',', $profileDetails->favorite_topics);
      }

      // Get subjects if available
       $userSubjects= [];
       if ($profileDetails && $profileDetails->user_subjects) {
        $userSubjects = explode(',', $profileDetails->user_subjects);
       }

       
                    // Get notifications
                    $notifications = Notification::with('sender')
                    ->where('user_id', auth()->user()->id)
                    ->where('type', 'New Connection')
                    ->where('read', 0)
                    ->orderByDesc('created_at')
                    ->get();
                $notificationCount = $notifications->count();
             
                // Get notifications
                    $messagenotifications = Notification::with('sender')
                    ->where('user_id', auth()->user()->id)
                    ->where('type', 'New Message')
                    ->where('read', 0)
                    ->orderByDesc('created_at')
                    ->get();
                $messagenotificationCount = $messagenotifications->count();

                $adminnotifications = Notification::with('sender')
                ->where('user_id', $user->id)
                ->where('type', 'New Admin Message')
                ->where('read', 0)
                ->orderByDesc('created_at')
                ->get();
         
                // Calculate the counts for each type of message
                $messagenotificationCount = $messagenotifications->count();
                $adminnotificationCount = $adminnotifications->count();
         
                // Total notification count
                $totalMessageNotificationCount = $messagenotificationCount + $adminnotificationCount;
               

        return view('profile.edit-profile', [
            'user' => $user,
            'profileDetails' => $profileDetails,
            'favoriteTopics' => $favoriteTopics,
            'userSubjects' => $userSubjects,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'messagenotifications' => $messagenotifications,
            'messagenotificationCount' => $messagenotificationCount,
            'subjects' => $subjects,
            'topics' => $topics,
            'adminnotifications' => $adminnotifications,
            'adminnotificationCount' => $adminnotificationCount,
            'totalMessageNotificationCount' => $totalMessageNotificationCount,
            
        ]);
    }

      // Update the user's profile.
      public function update(Request $request)
      {
          $user = Auth::user();
      
          // Update user's name and email if provided
          if ($request->filled('first_name')) {
              $user->first_name = $request->input('first_name');
          }
          if ($request->filled('surname')) {
              $user->surname = $request->input('surname');
          }
          if ($request->filled('email')) {
              $user->email = $request->input('email');
          }
          $user->save();

         //update institution details name
         $institutionDetails = $user->institutionDetails;
         if ($institutionDetails) {
            $institutionDetails->institution_name = $request->input('institution_name');
            $institutionDetails->save();
        }

           //update institution details location
           $institutionDetails = $user->institutionDetails;
           if ($institutionDetails) {
              $institutionDetails->institution_location = $request->input('institution_location');
              $institutionDetails->save();
          }

            //update institution details website
            $institutionDetails = $user->institutionDetails;
            if ($institutionDetails) {
               $institutionDetails->institution_website = $request->input('institution_website');
               $institutionDetails->save();
           }

      
          // Handle profile picture update
          if ($request->hasFile('profile_picture')) {
      
              // Delete previous profile picture if it exists
              if ($user->profile_picture) {
                  Storage::disk('public')->delete($user->profile_picture);
              }
      
              $file = $request->file('profile_picture');
              $fileName = 'profile_' . time() . '.' . $file->getClientOriginalExtension(); // Generate a unique file name
              $filePath = $file->storeAs('profile-images', $fileName, 'public'); // Store the file in public/profile-images
              $user->profile_picture = $filePath; // Save the image path to the database
              $user->save();
          }
      
          // Update social media links for teacher
          $teacherDetails = $user->teacherDetails;
          if ($teacherDetails) {
              $teacherDetails->socials = $request->input('socials');
              $teacherDetails->save();
          }
      
          // Update social media links for student
          $studentDetails = $user->studentDetails;
          if ($studentDetails) {
              $studentDetails->socials = $request->input('socials');
              $studentDetails->save();
          }
      
          // Update name of institution
          $institutionDetails = $user->institutionDetails;
          if ($institutionDetails) {
              $institutionDetails->socials = $request->input('socials');
              $institutionDetails->save();
          }
      
          // Update profile details for teacher, student, or institution based on profile type
          if ($user->profile_type === 'teacher' && $teacherDetails && $request->filled('about')) {
              $teacherDetails->about = $request->input('about');
              $teacherDetails->save();
          } elseif ($user->profile_type === 'student' && $studentDetails && $request->filled('about')) {
              $studentDetails->about = $request->input('about');
              $studentDetails->save();
          } elseif ($user->profile_type === 'institution' && $institutionDetails && $request->filled('institution_about')) {
              $institutionDetails->institution_about = $request->input('institution_about');
              $institutionDetails->save();
          }
      
          // Update favorite topics and subjects for teacher and student
          if ($teacherDetails && $request->filled('favorite_topics')) {
              $existingTopics = explode(',', $teacherDetails->favorite_topics);
              $newTopics = $request->input('favorite_topics');
              $mergedTopics = array_unique(array_merge($existingTopics, $newTopics));
              $teacherDetails->favorite_topics = implode(',', $mergedTopics);
              $teacherDetails->favorite_topics = ltrim($teacherDetails->favorite_topics, ',');
              $teacherDetails->save();
          }
      
          if ($teacherDetails && $request->filled('user_subjects')) {
              $existingSubjects = explode(',', $teacherDetails->user_subjects);
              $newSubjects = $request->input('user_subjects');
              $mergedSubjects = array_unique(array_merge($existingSubjects, $newSubjects));
              $teacherDetails->user_subjects = implode(',', $mergedSubjects);
              $teacherDetails->user_subjects = ltrim($teacherDetails->user_subjects, ',');
              $teacherDetails->save();
          }
      
          if ($studentDetails && $request->filled('favorite_topics')) {
              $existingTopics = explode(',', $studentDetails->favorite_topics);
              $newTopics = $request->input('favorite_topics');
              $mergedTopics = array_unique(array_merge($existingTopics, $newTopics));
              $studentDetails->favorite_topics = implode(',', $mergedTopics);
              $studentDetails->favorite_topics = ltrim($studentDetails->favorite_topics, ',');
              $studentDetails->save();
          }
      
          if ($studentDetails && $request->filled('user_subjects')) {
              $existingSubjects = explode(',', $studentDetails->user_subjects);
              $newSubjects = $request->input('user_subjects');
              $mergedSubjects = array_unique(array_merge($existingSubjects, $newSubjects));
              $studentDetails->user_subjects = implode(',', $mergedSubjects);
              $studentDetails->user_subjects = ltrim($studentDetails->user_subjects, ',');
              $studentDetails->save();
          }

          if ($institutionDetails && $request->filled('favorite_topics')) {
              $existingTopics = explode(',', $institutionDetails->favorite_topics);
              $newTopics = $request->input('favorite_topics');
              $mergedTopics = array_unique(array_merge($existingTopics, $newTopics));
              $institutionDetails->favorite_topics = implode(',', $mergedTopics);
              $institutionDetails->favorite_topics = ltrim($institutionDetails->favorite_topics, ',');
              $institutionDetails->save();
        }
    
        if    ($institutionDetails && $request->filled('user_subjects')) {
              $existingSubjects = explode(',', $institutionDetails->user_subjects);
              $newSubjects = $request->input('user_subjects');
              $mergedSubjects = array_unique(array_merge($existingSubjects, $newSubjects));
              $institutionDetails->user_subjects = implode(',', $mergedSubjects);
              $institutionDetails->user_subjects = ltrim($institutionDetails->user_subjects, ',');
              $institutionDetails->save();
        }
      
          Alert::Success('Profile updated successfully');
          return redirect()->route('profile.edit');
      }      

        // Show the user's profile.
        public function show($username)
        {
            $user = User::where('username', $username)->firstOrFail();

     // Get the user's profile details based on their profile type
        $profileDetails = null;
        if ($user->profile_type === 'teacher') {
            $profileDetails = $user->teacherDetails;
        } elseif ($user->profile_type === 'student') {
            $profileDetails = $user->studentDetails;
        } elseif ($user->profile_type === 'institution') {
            $profileDetails = $user->institutionDetails;
        } elseif ($user->profile_type === 'other') {
            $profileDetails = $user->otherDetails;
        }

          // Convert socials string to array if it's stored as JSON in the database
          if ($profileDetails->socials) {
            $profileDetails->socials = json_decode($profileDetails->socials, true);
        }

        // Get the user's posts (as viewed by another user)
            $posts = $user->posts()
            ->with([
                'comments' => function ($query) {
                    // Exclude comments from users who have been deleted (archived)
                    $query->whereHas('user', function ($query) {
                        $query->whereNull('deleted_at');
                    });
                },
                'reposter', // Include reposter info
                'originalUser' => function ($query) {
                    // Exclude posts from deleted (archived) users
                    $query->whereNull('deleted_at');
                },
                'originalPost' => function ($query) {
                    // Ensure comments are excluded from deleted (archived) users on original posts
                    $query->with([
                        'comments' => function ($query) {
                            $query->whereHas('user', function ($query) {
                                $query->whereNull('deleted_at');
                            });
                        }
                    ]);
                }
            ])
            ->whereHas('user', function ($query) {
                $query->whereNull('deleted_at'); // Ensure that only posts from active (non-archived) users are fetched
            })
            ->where(function ($query) {
                $query->where('is_repost', false) // Regular post
                    ->orWhere(function ($query) {
                        $query->where('is_repost', true) // Repost case
                            ->whereHas('originalUser', function ($query) {
                                $query->whereNull('deleted_at'); // Only include reposts where the original user's deleted_at is null (non-archived)
                            });
                    });
            })
            ->orderBy('created_at', 'desc')
            ->get();

            
                   // Get notifications
                   $notifications = Notification::with('sender')
                   ->where('user_id', auth()->user()->id)
                   ->where('type', 'New Connection')
                   ->where('read', 0)
                   ->orderByDesc('created_at')
                   ->get();
               $notificationCount = $notifications->count();
            
               // Get notifications
                   $messagenotifications = Notification::with('sender')
                   ->where('user_id', auth()->user()->id)
                   ->where('type', 'New Message')
                   ->where('read', 0)
                   ->orderByDesc('created_at')
                   ->get();
               $messagenotificationCount = $messagenotifications->count();

               $adminnotifications = Notification::with('sender')
               ->where('user_id', $user->id)
               ->where('type', 'New Admin Message')
               ->where('read', 0)
               ->orderByDesc('created_at')
               ->get();
        
               // Calculate the counts for each type of message
               $messagenotificationCount = $messagenotifications->count();
               $adminnotificationCount = $adminnotifications->count();
        
               // Total notification count
               $totalMessageNotificationCount = $messagenotificationCount + $adminnotificationCount;
              
              

          // Get followers count
          $followersCount = $user->followers()->count();

            return view('profile.show', [
            'user' => $user,
            'profileDetails' => $profileDetails,
            'posts' => $posts,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'followersCount' => $followersCount,
            'messagenotifications' => $messagenotifications,
            'messagenotificationCount' => $messagenotificationCount,
            'adminnotifications' => $adminnotifications,
            'adminnotificationCount' => $adminnotificationCount,
            'totalMessageNotificationCount' => $totalMessageNotificationCount,
            ]);
        }
}
