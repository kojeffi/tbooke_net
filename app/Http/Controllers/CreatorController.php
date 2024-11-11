<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Creator;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;


class CreatorController extends Controller
{
    public function store(Request $request)
    {

        $user = Auth::user();
        $firstName = $user->first_name;
        $surname = $user->surname; 

        $validatedData = $request->validate([
            'creator_subjects' => 'required|array',
            'creator_subjects.*' => 'string', 
            'creator_expertise' => 'required|array',
            'creator_expertise.*' => 'string', 
            'the_why' => 'required|string',
        ]);
    
        // Convert arrays to comma-separated strings
        $creator_subjects = implode(',', $validatedData['creator_subjects']);
        $creator_expertise = implode(',', $validatedData['creator_expertise']);
        
      
    
        $creator = new Creator();
        $creator->creator_subjects = $creator_subjects;
        $creator->creator_expertise = $creator_expertise;
        $creator->the_why = $validatedData['the_why'];
        $creator->first_name = $firstName; 
        $creator->surname = $surname; 
        $creator->user_id = auth()->id();
        $creator->save();

        Alert::Success('Success, you can now create content on Tbooke');
        return response()->json([
            'message' => 'Application done successfully',
            'redirect_url' => route('tbooke-learning')
        ], 200);
    }

    // Other methods can be added based on your application requirements
}
