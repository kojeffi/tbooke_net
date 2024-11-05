@include('includes.header')
{{-- Sidebar --}}
@include('includes.sidebar')
@include('sweetalert::alert')

{{-- Topbar --}}
<div class="main">
    @include('includes.topbar')
    {{-- Main Content --}}
    <main class="content">
        <div class="container-fluid p-0">
            <div>
               <div class="row content">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <!-- Left-aligned heading -->
                            <h1 class="h3 mb-0">Tbooke Blueboard</h1>
                            <!-- Right-aligned buttons with space -->
                            <div class="d-flex">
                                <a href="{{ route('blueboard.userPosts', auth()->user()->username) }}" class="btn btn-secondary me-3">My Blueboard Posts</a>
                                <a href="{{ route('blueboard.create') }}" class="btn btn-primary">Create Blueboard Post</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Display Existing Blueboard Posts --}}
                <div class="row">
                    <div class="col-md-12">
                        @foreach($posts as $post)
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">{{ $post->title }}</h5>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">{{ $post->content }}</p>
                                    <p class="card-text"><small class="text-muted">Posted by {{ $post->user->first_name }} on {{ $post->created_at->format('d M Y, h:i A') }}</small></p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </main>
    {{-- footer --}}
    @include('includes.footer')
</div>