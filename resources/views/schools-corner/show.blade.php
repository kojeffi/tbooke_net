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
                            <h1 class="h3">{{ $school->name }}</h1>
                            <img src="{{ asset('storage/' . $school->thumbnail) }}" alt="{{ $school->name }} Logo" class="school-logo">
                        </div>
                    </div>

                    {{-- Programs and Courses --}}
                    <div>
                        <h2 class="h4">Programs and Courses</h2>
                        <div class="row">
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">STEM Program</h5>
                                        <p class="card-text">Comprehensive science, technology, engineering...</p>
                                        <a href="#" class="btn btn-outline-primary">Learn More</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Arts Program</h5>
                                        <p class="card-text">Extensive arts program including visual arts, music, and theater.</p>
                                        <a href="#" class="btn btn-outline-primary">Learn More</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Sports Program</h5>
                                        <p class="card-text">Wide range of sports activities and teams to join.</p>
                                        <a href="#" class="btn btn-outline-primary">Learn More</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Student and Parent Resources --}}
                    <div>
                        <h2 class="h4">Student and Parent Resources</h2>
                        <div class="row">
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Homework Help</h5>
                                        <p class="card-text">Resources and tools to help students with their homework.</p>
                                        <a href="#" class="btn btn-outline-primary">Access Resource</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Parent Portal</h5>
                                        <p class="card-text">Information and resources for parents to support their children...</p>
                                        <a href="#" class="btn btn-outline-primary">Access Resource</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Counseling Services</h5>
                                        <p class="card-text">Support services for students’ mental and emotional well-being.</p>
                                        <a href="#" class="btn btn-outline-primary">Access Resource</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- News and Events --}}
                    <div>
                        <h2 class="h4">News and Events</h2>
                        <div class="list-group">
                            <a href="#" class="list-group-item list-group-item-action">
                                <h5 class="mb-1">Annual Science Fair</h5>
                                <p class="mb-1">Join us for the annual science fair showcasing student projects.</p>
                                <small>March 20, 2024</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <h5 class="mb-1">Spring Concert</h5>
                                <p class="mb-1">Enjoy performances by our talented students at the spring concert.</p>
                                <small>April 10, 2024</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <h5 class="mb-1">Parent-Teacher Meetings</h5>
                                <p class="mb-1">Schedule your meetings with teachers to discuss student progress.</p>
                                <small>May 5, 2024</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    {{-- footer --}}
    @include('includes.footer')
</div>

<style>
    .school-logo {
        height: 100px;
        width: auto;
        object-fit: contain;
    }
</style>
