<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\TbookeLearning; 
use Illuminate\Http\Request;
use App\Models\Notification;

class ContentController extends Controller
{
    public function show($slug)
    {
        $user = Auth::user();
        $content = TbookeLearning::where('slug', $slug)->firstOrFail();
        $categories = explode(',', $content->content_category); 
    
        $relatedContent = TbookeLearning::where('id', '!=', $content->id)
                            ->where(function ($query) use ($categories) {
                                foreach ($categories as $category) {
                                    $query->orWhere('content_category', 'LIKE', '%' . $category . '%');
                                }
                            })
                            ->take(4) // Limit the number of related content items
                            ->get();
                            
        return view('tbooke-learning.show', [
            'user' => $user,
            'content' => $content,
            'relatedContent' => $relatedContent,
        ]);
    }
    
}
