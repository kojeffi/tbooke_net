@include('includes.header')
@include('includes.sidebar')
@include('sweetalert::alert')
<div class="main">
    @include('includes.topbar')
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-md-12 video-embed">
                    <h1 class="h3">{{ $liveClass->class_name }}</h1>
                    <p>By {{ $liveClass->creator_name }}</p>
                    <p>{{ $liveClass->class_description }}</p>
                    @if ($liveClass->video_room_name)
                        <iframe src="https://{{ env('METERED_DOMAIN') }}/{{ $liveClass->video_room_name }}" width="100%" height="832px" allow="microphone; camera"></iframe>
                    @else
                        <p>No stream available for this class.</p>
                    @endif
                </div>
            </div>
        </div>
    </main>
    @include('includes.footer')
</div>
