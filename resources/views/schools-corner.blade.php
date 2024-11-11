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
                            <h1 class="h3">Schools Corner</h1>
                            <div>
                                <a href="{{ route('schools.create') }}" class="btn btn-primary">Add School</a>
                                <a href="{{ route('schools.userschools', auth()->user()->username) }}" class="btn btn-secondary ml-2">My Schools</a>
                            </div>
                        </div>                        
                    </div>

                    @foreach($schools as $school)
                    <div class="col-12 col-md-4 mb-3">
                        <div class="card">
                            <img class="card-img-top school-img" src="{{ asset('storage/' . $school->thumbnail) }}" style="height: 200px; object-fit: cover;">
                            <div class="card-header school">
                                <h5 class="card-title school-name">{{ $school->name }}</h5>
                            </div>
                            <div style="padding-top: 0" class="card-body">
                                <p class="card-text">{{ Str::limit($school->description, 100) }}</p>
                                <a href="{{ route('schools.show', $school->slug) }}" class="card-link school-contact-button">Learn More</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </main>
    {{-- footer --}}
    @include('includes.footer')
</div>

<style>
    .school-img {
        height: 182px;
        object-fit: cover;
        width: 100%;
    }
</style>