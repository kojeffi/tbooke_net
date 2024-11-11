@include('includes.header')
{{-- Sidebar --}}
@include('includes.sidebar')

{{-- Topbar --}}
<div class="main">
    @include('includes.topbar')
    {{-- Main Content --}}
    <main class="content">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12 col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h1 class="h3 mb-3">{{ $content->content_title }}</h1>
                        <div class="creator-content">{!! htmlspecialchars_decode($content->content) !!}</div>
                        <!-- Media Gallery -->
              <!-- Media Gallery -->
@if ($content->media_files && is_array(json_decode($content->media_files, true)))
    <div class="media-gallery mt-4">
        <div class="row g-2 mt-1">
            @foreach (json_decode($content->media_files, true) as $media)
                @php
                    $extension = pathinfo($media, PATHINFO_EXTENSION);
                @endphp
                <div class="col-12 col-md-6 col-lg-4">
                    @if (in_array($extension, ['jpeg', 'jpg', 'png', 'gif']))
                        <a href="{{ asset('storage/' . $media) }}" data-lightbox="content-gallery-{{ $content->id }}">
                            <img src="{{ asset('storage/' . $media) }}" class="img-fluid mb-2" alt="content-media">
                        </a>
                    @elseif (in_array($extension, ['mp4', 'mov', 'avi', 'wmv']))
                        <video controls class="img-fluid mb-2" style="max-width: 100%">
                            <source src="{{ asset('storage/' . $media) }}" type="video/{{ $extension }}">
                            Your browser does not support the video tag.
                        </video>
                    @elseif (in_array($extension, ['pdf', 'ppt', 'doc', 'docx']))
                        <a href="{{ asset('storage/' . $media) }}" target="_blank" class="file-link">
                            View {{ strtoupper($extension) }} File
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif

                    </div>   
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Related Content</h5>
                    </div>
                    <div class="card-body">
                        @foreach ($relatedContent as $related)
                            <div class="related-item mb-3">
                                <img class="img-fluid mb-2" src="{{ asset('storage/' . $related->content_thumbnail) }}" alt="">
                                <h6 class="related-title">{{ $related->content_title }}</h6>
                                <a href="{{ route('content.show', $related->slug) }}" class="btn btn-primary btn-sm">View Content</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>    
    </div>
</main>

    {{-- footer --}}
    @include('includes.footer')
</div>
