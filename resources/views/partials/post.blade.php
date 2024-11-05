@if (isset($post->content))
    <!-- Original Post -->
    <div class="original-post">
        <div class="post-content">
            <p>{{ $post->content }}</p>
            <!-- Add other fields as needed -->
        </div>
        <!-- Display comments if available -->
        @if ($post->comments->isNotEmpty())
            <div class="comments-section">
                <h4>Comments:</h4>
                <ul>
                    @foreach ($post->comments as $comment)
                        <li>{{ $comment->content }}</li>
                        <!-- Add other fields as needed -->
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@elseif (isset($post->originalPost))
    <!-- Reposted Post -->
    <div class="reposted-post">
        <div class="post-content">
            <p>{{ $post->originalPost->content }}</p>
            <!-- Display original post content -->
        </div>
        <!-- Display comments of the original post if available -->
        @if ($post->originalPost->comments->isNotEmpty())
            <div class="comments-section">
                <h4>Original Post Comments:</h4>
                <ul>
                    @foreach ($post->originalPost->comments as $comment)
                        <li>{{ $comment->content }}</li>
                        <!-- Add other fields as needed -->
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif
