<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\PostRepostedMail;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use DOMDocument;
use Illuminate\Support\Facades\Mail;

class PostController extends Controller
{
    private $youtubeApiKey = 'AIzaSyBdy2-YXcqsNfpI2IyYJIMNuyHAH3sKpJ8'; // Your YouTube API key

    // Store a new post
    public function store(Request $request)
    {
        try {
            // Validate post data
            $validatedData = $request->validate([
                'content' => 'nullable|string',
                'media_path.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mp3,webm,mov,avi,wmv,wav',
            ]);

            $post = new Post();
            $post->content = $validatedData['content'];
            $post->user_id = auth()->id();

            // Check if the content contains a URL
            if (preg_match('/\bhttps?:\/\/\S+/i', $post->content)) {
                if (strpos($post->content, 'youtube.com') !== false || strpos($post->content, 'youtu.be') !== false) {
                    // Fetch YouTube data
                    $linkPreview = $this->fetchYouTubeData($post->content);
                    if ($linkPreview) {
                        $post->link_preview = json_encode($linkPreview); // Store YouTube data as JSON
                    }
                } else {
                    // Fetch OpenGraph data
                    $ogData = $this->fetchOpenGraphData($post->content);
                    if ($ogData) {
                        $post->link_preview = json_encode($ogData); // Store OpenGraph data as JSON
                    }
                }
            }

            // Handle multiple file uploads if media is present
            if ($request->hasFile('media_path')) {
                $mediaPaths = [];
                foreach ($request->file('media_path') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('content-media', $fileName, 'public');
                    $mediaPaths[] = $filePath;
                }
                $post->media_path = $mediaPaths; // Assign array to the JSON attribute
            }

            $post->save();

            return response()->json(['message' => 'Post created successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error creating post: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create post'], 500);
        }
    }

    // Fetch OpenGraph metadata for a given URL
    private function fetchOpenGraphData($url)
    {
        $client = new Client();
        try {
            // Send a GET request to the URL
            $response = $client->get($url);
            $html = (string) $response->getBody();

            // Parse the HTML to extract OpenGraph tags
            $dom = new DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

            $metaTags = $dom->getElementsByTagName('meta');
            $ogData = [];

            // Loop through meta tags and fetch OpenGraph data
            foreach ($metaTags as $tag) {
                if ($tag->getAttribute('property') == 'og:title') {
                    $ogData['title'] = $tag->getAttribute('content');
                }
                if ($tag->getAttribute('property') == 'og:description') {
                    $ogData['description'] = $tag->getAttribute('content');
                }
                if ($tag->getAttribute('property') == 'og:image') {
                    $ogData['image'] = $tag->getAttribute('content');
                }
            }

            // Default fallback values if OpenGraph data isn't found
            if (!isset($ogData['title'])) {
                $ogData['title'] = 'No title available';
            }
            if (!isset($ogData['description'])) {
                $ogData['description'] = 'No description available';
            }
            if (!isset($ogData['image'])) {
                $ogData['image'] = null;
            }

            return $ogData;

        } catch (\Exception $e) {
            Log::error('Error fetching OpenGraph data: ' . $e->getMessage());
            return null; // Return null if an error occurs
        }
    }

    // Fetch YouTube metadata for a given URL
    private function fetchYouTubeData($url)
    {
        $videoId = null;

        // Extract video ID from the URL
        if (preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            $videoId = $matches[1];
        }

        if ($videoId) {
            $client = new Client();
            $response = $client->get("https://www.googleapis.com/youtube/v3/videos?id={$videoId}&key={$this->youtubeApiKey}&part=snippet");
            $data = json_decode($response->getBody());

            if (!empty($data->items)) {
                $videoData = $data->items[0]->snippet;

                return [
                    'title' => $videoData->title,
                    'description' => $videoData->description,
                    'image' => $videoData->thumbnails->high->url,
                    'domain' => 'youtube.com',
                ];
            }
        }

        return null; // Return null if no data is found
    }

    public function repostPost(Request $request, Post $post)
    {
        $user = auth()->user();

        // Check if the user has already reposted this post
        $alreadyReposted = Post::where('user_id', $user->id)
                                ->where('original_post_id', $post->id)
                                ->exists();

        if ($alreadyReposted) {
            return response()->json(['message' => 'You have already reposted this post.'], 400);
        }

        // Create a new post entry for the repost
        Post::create([
            'user_id' => $user->id,
            'content' => $post->content,
            'original_post_id' => $post->id, // Track the original post
            'original_user_id' => $post->user_id, // Track the original user
            'is_repost' => true,
            'media_path' => $post->media_path, // Include the media_path
        ]);

        // Increment the repost count on the original post
        $post->increment('repost_count');

        // Send email notification to the original post creator
        Mail::to($post->user->email)->send(new PostRepostedMail($post, $user));

        return response()->json(['message' => 'Post reposted successfully!']);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        // Check if the post is a repost
        if ($post->is_repost && $post->originalPost) {
            // Decrement the repost_count on the original post
            $post->originalPost->decrement('repost_count');
        }

        if ($post->delete()) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false], 500);
        }
    }

    
}

