{{-- resources.blade.php --}}
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
            <div class="row content">
                <div class="col-md-5 d-flex justify-content-start align-items-center">
                    <h1 class="h3 d-inline align-middle">Tbooke Learning</h1>
                </div>
                <div class="col-md-7">
                    <div class="d-md-block">
                        <div class="text-end">
                            <a href="{{ route('live-classes.create') }}" class="btn btn-secondary">Schedule a Live Class</a>
                            <a href="{{ route('tbooke-learning.create') }}" class="btn btn-primary">Create Content</a>
                            @if(auth()->check())
                                <a href="{{ route('tbooke-learning.user', auth()->user()->username) }}" class="btn btn-info my-conent">My Content</a>
                            @endif
                         </div>
                    </div>
                </div>
            </div>

            <!-- Single Search Input -->
            <div class="row mb-3 search-input">
                <div class="col-md-12">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" id="search" class="form-control" placeholder="Search by title, creator, or categories">
                </div>
            </div>

            <div class="row content" id="content-container">
               @foreach($contents as $content)
                    <div class="col-12 col-md-4 content-item" 
                        data-title="{{ $content->content_title }}" 
                        data-creator-first-name="{{ $content->user->first_name }}" 
                        data-creator-last-name="{{ $content->user->surname }}" 
                        data-categories="{{ $content->content_category }}">
                        <div class="card tbooke-learning-card">
                            <div class="content-img-box">
                            <a href="{{ route('content.show', $content->slug) }}"><img class="card-img-top content-thumbnail" src="{{ $content->content_thumbnail ? asset('storage/' . $content->content_thumbnail) : asset('default-images/default-bg.jpg') }}" alt=""></a>
                            </div>
                            <div class="card-header author-category">
                                @if ($content->user->profile_type == 'institution')
                                   <h5 class="card-title author">{{ $content->user->institutionDetails->institution_name }}</h5>   
                                 @else
                                 <h5 class="card-title author">{{ $content->user->first_name }} {{ $content->user->surname }}</h5>   
                                @endif
                                <a href="{{ route('content.show', $content->slug) }}" class="content-title"><h5 class="card-title content-title">{{ $content->content_title }}</h5></a>
                                <div class="content-categories">
                                    @foreach(explode(',', $content->content_category) as $category)
                                    <a href="#" class="badge bg-primary me-1 my-1">{{ $category }}</a>
                                    @endforeach
                                </div>
                                  <div class="content-stats">
                                    <a href="#" class="card-link stat-link">
                                        <i class="feather-sm content-stats-icon" data-feather="calendar"></i> 
                                        <span class="content-stats-span">{{ $content->created_at->format('F j, Y') }}</span>
                                    </a>
                                  </div>
                            </div>
                            <div class="card-body tbooke-content">
                                <p class="card-text content-desc">{{ Str::limit(strip_tags($content->content), 50) }}</p>
                                <a href="{{ route('content.show', $content->slug) }}" class="card-link start-learning-button">Start Learning</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </main>
    {{-- footer --}}
    @include('includes.footer')
</div>

<!-- JavaScript for Search Filtering -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('search');
        const contentItems = document.querySelectorAll('.content-item');

        searchInput.addEventListener('input', function () {
            const query = searchInput.value.toLowerCase();

            contentItems.forEach(function (item) {
                const title = item.getAttribute('data-title').toLowerCase();
                const creatorFirstName = item.getAttribute('data-creator-first-name').toLowerCase();
                const creatorLastName = item.getAttribute('data-creator-last-name').toLowerCase();
                const categories = item.getAttribute('data-categories').toLowerCase();

                if (title.includes(query) || creatorFirstName.includes(query) || creatorLastName.includes(query) || categories.includes(query)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
</script>