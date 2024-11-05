@include('includes.header')
{{-- Sidebar --}}
@include('includes.sidebar')
@include('sweetalert::alert')

@if(session('success'))
    <script>
        Swal.fire({
            title: 'Success!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    </script>
@endif

{{-- Main Content --}}
<div class="main">
    @include('includes.topbar')

    <main class="content">
        <div class="container-fluid p-0">
            <div class="row">
                {{-- Left Sidebar - 5/12 Column --}}
                <div class="col-lg-5 col-md-5 col-sm-12 mb-4">
                    <div class="card text-center">
                        <img src="{{ $group->thumbnail ? asset('storage/' . $group->thumbnail) : asset('default-images/group.png') }}" alt="{{ $group->name }} thumbnail" class="card-img-top img-fluid" style="height: 150px; object-fit: cover;">
                        <div class="card-body">
                            <h2>{{ $group->name }}</h2>
                            <p><strong>Members:</strong> {{ $group->members()->count() }}</p>
                             {{-- Created By --}}
                            <p>
                                <strong>Created by:</strong><br>
                                 <a href="{{ $group->creator->id === auth()->id() ? route('profile.showOwn') : route('profile.show', $group->creator->username) }}">
                                    <img src="{{ $group->creator->profile_picture ? asset('storage/' . $group->creator->profile_picture) : asset('default-images/avatar.png') }}" alt="{{ $group->creator->first_name }}'s Profile Picture" class="rounded-circle" style="width: 30px; height: 30px; margin-right: 5px;">
                                    
                                    @if ($group->creator->profile_type === 'institution')
                                        {{-- Show institution name --}}
                                        {{ $group->creator->institutionDetails->institution_name }}
                                    @else
                                        {{-- Show individual user name --}}
                                        {{ $group->creator->first_name . ' ' . $group->creator->surname }}
                                    @endif
                                </a>
                            </p>
                            <h4>About Group:</h4>
                            <p class="card-text">{{ $group->description }}</p>
                            
                            @if($group->members()->where('user_id', auth()->id())->exists())
                                <button class="btn btn-sm btn-secondary" disabled>Joined</button>
                            @else
                                <form action="{{ route('groups.join', $group->slug) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary">Join</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Right Content - 7/12 Column --}}
                <div class="col-lg-7 col-md-7 col-sm-12">
                    @if($group->members()->where('user_id', auth()->id())->exists())
                        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createPostModal">
                            Create Post
                        </button>
                    @endif
                    
<h4>Group Posts</h4>
@forelse ($groupPosts->sortByDesc('created_at') as $post)
    <div class="card mb-3">
        <div class="card-body">
            @php
                // Determine if the post is a repost and get the original post
                $isRepost = $post instanceof \App\Models\GroupRepost;
                $originalPost = $isRepost ? $post->post : $post; // Get the original post
                $reposter = $isRepost ? $post->user : null; // Get the user who reposted if applicable
            @endphp
            
            <div class="d-flex align-items-center mb-2">
                @if ($isRepost && $reposter)
                    <a href="{{ $reposter->id === auth()->id() ? route('profile.showOwn') : route('profile.show', $reposter->username) }}">
                        <img src="{{ $reposter->profile_picture? asset('storage/' . $reposter->profile_picture) : asset('default-images/avatar.png')  }}" alt="{{ $reposter->first_name }}'s Profile" class="rounded-circle" style="width: 40px; height: 40px; margin-right: 10px;">
                    </a>
                    <span>
                        <a href="{{ $reposter->id === auth()->id() ? route('profile.showOwn') : route('profile.show', $reposter->username) }}">
                            @if ($reposter->profile_type === 'institution')
                                {{ $reposter->institutionDetails->institution_name }}
                            @else
                                {{ $reposter->first_name . ' ' . $reposter->surname }}
                            @endif
                        </a>
                    </span>
                    <span class="mx-1">reposted this</span>
                    <span class="text-muted mx-1">{{ $post->created_at->diffForHumans() }}</span> <!-- Added mx-1 -->
                @endif
            </div>

            <div class="reposted-post" style="margin-left: 50px;"> <!-- Adjust left margin for indentation -->
                <div class="d-flex align-items-center mb-2">
                    <a href="{{ $originalPost->user->id === auth()->id() ? route('profile.showOwn') : route('profile.show', $originalPost->user->username) }}">
                        <img src="{{ $originalPost->user->profile_picture ? asset('storage/' . $originalPost->user->profile_picture) : asset('default-images/avatar.png') }}" alt="{{ $originalPost->user->first_name }}'s Profile" class="rounded-circle" style="width: 40px; height: 40px; margin-right: 10px;">
                    </a>
                    <span>
                        <a href="{{ $originalPost->user->id === auth()->id() ? route('profile.showOwn') : route('profile.show', $originalPost->user->username) }}">
                            @if ($originalPost->user->profile_type === 'institution')
                                {{ $originalPost->user->institutionDetails->institution_name }}
                            @else
                                {{ $originalPost->user->first_name . ' ' . $originalPost->user->surname }}
                            @endif
                        </a>
                    </span>
                    <span class="text-muted mx-1">{{ $originalPost->created_at->diffForHumans() }}</span> <!-- Added mx-1 -->
                </div>

                <p>{{ $originalPost->content }}</p>
                @if($originalPost->media)
                    <div class="media-content">
                        <img src="{{ asset('storage/' . $originalPost->media) }}" alt="Media" class="img-fluid">
                    </div>
                @endif
                
                <div>
                    <i class="feather-sm" data-feather="thumbs-up"></i>{{ $originalPost->likes()->count() }}
                </div>
                <div class="engagement-buttons d-inline">
                    @if(!$originalPost->likes()->where('user_id', auth()->id())->exists())
                        <form action="{{ route('group.posts.like', $originalPost->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="feather-sm" data-feather="thumbs-up"></i> Like
                            </button>
                        </form>
                    @else
                        <button class="btn btn-sm btn-secondary" disabled>
                            <i class="feather-sm" data-feather="thumbs-up"></i> Liked
                        </button>
                    @endif
                    
                    @if($group->members->contains(auth()->id()))
                        <button class="btn btn-sm btn-outline-primary comment-toggle" type="button">
                            <i class="feather-sm" data-feather="message-square"></i> Comment
                        </button>
                    @else
                        <button class="btn btn-sm btn-secondary" type="button" disabled>
                            <i class="feather-sm" data-feather="message-square"></i> Join to Comment
                        </button>
                    @endif
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#repostModal{{ $originalPost->id }}">
                        <i class="feather-sm" data-feather="repeat"></i> Repost
                    </button>
                </div>
                
                <div class="repost-comment-count d-inline float-end">
                    <span><i class="feather-sm" data-feather="message-square"></i>{{ $originalPost->comments()->count() }}</span>
                    <span><i class="feather-sm" data-feather="repeat"></i>{{ $originalPost->reposts()->count() }}</span>
                </div>

                {{-- Comments --}}
                <div class="group-comment-box" style="display: none">
                    @foreach ($originalPost->comments as $comment)
                        <div class="flex-grow-1 comment-item-inner-box mb-2">
                            <small class="float-end text-navy">{{ $comment->created_at->diffForHumans() }}</small>
                            <div class="text-muted p-2 mt-1">
                                <div class="d-flex align-items-center">
                                    <a href="{{ $comment->user->id === auth()->id() ? route('profile.showOwn') : route('profile.show', $comment->user->username) }}">
                                        <img src="{{ $comment->user->profile_picture ? asset('storage/' . $comment->user->profile_picture) : asset('default-images/avatar.png') }}" alt="{{ $comment->user->first_name }}'s Profile" class="rounded-circle me-2" style="width: 30px; height: 30px;">
                                    </a>
                                    <span>
                                        <a href="{{ $comment->user->id === auth()->id() ? route('profile.showOwn') : route('profile.show', $comment->user->username) }}">
                                            @if ($comment->user->profile_type === 'institution')
                                                {{ $comment->user->institutionDetails->institution_name }}
                                            @else
                                                {{ $comment->user->first_name . ' ' . $comment->user->surname }}
                                            @endif
                                        </a>
                                    </span>
                                </div>
                                <div class="ml-2">{{ $comment->content }}</div>
                            </div>
                        </div>
                    @endforeach

                    {{-- Comment Form --}}
                    <form action="{{ route('group.posts.comment', $originalPost->id) }}" method="POST" class="mt-2">
                        @csrf
                        <textarea name="content" class="form-control" rows="2" placeholder="Add a comment" required></textarea>
                        <button type="submit" class="btn btn-sm btn-primary mt-2">Comment</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="repostModal{{ $originalPost->id }}" tabindex="-1" aria-labelledby="repostModalLabel{{ $originalPost->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('group.posts.repost', $originalPost->id) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="repostModalLabel{{ $originalPost->id }}">Repost</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <textarea name="content" class="form-control" placeholder="Add a comment (optional)"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Repost</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@empty
    <p>No posts available in this group.</p>
@endforelse




                </div>
            </div>
        </div>
    </main>

    @include('includes.footer')
</div>

<div class="modal fade" id="createPostModal" tabindex="-1" aria-labelledby="createPostModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createPostModalLabel">Create a Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createPostForm" action="{{ route('group.posts.store', $group->slug) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="postContent" class="form-label">Content</label>
                        <textarea class="form-control" id="postContent" name="content" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="postMedia" class="form-label">Media (Optional)</label>
                        <input class="form-control" type="file" id="postMedia" name="media">
                    </div>
                    <button type="submit" class="btn btn-primary">Post</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for handling form submission (if using AJAX) -->
<script>
    document.getElementById('createPostForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var actionUrl = this.action;
        
        fetch(actionUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('createPostModal').classList.remove('show');
                Swal.fire({
                    title: 'Success!',
                    text: 'Post created successfully!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => location.reload());
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'An error occurred. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred. Please try again.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
    });

    // Toggle comment box functionality
    document.querySelectorAll('.comment-toggle').forEach(button => {
        button.addEventListener('click', function() {
            const commentBox = this.closest('.card-body').querySelector('.group-comment-box');
            commentBox.style.display = commentBox.style.display === 'none' ? 'block' : 'none';
        });
    });
</script>

<!-- Feather icons script -->
<script>
    feather.replace();
</script>
