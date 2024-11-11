@include('includes.header')
{{-- Sidebar --}}
@include('includes.sidebar')

{{-- Topbar --}}
<div class="main">
    @include('includes.topbar')
    {{-- Main Content --}}
    <main class="content">
        <div class="container-fluid p-0">
            <h1 class="h3 mb-3">Dashboard</h1>
        </div>
        <div class="row">
            <div class="col-md-6">
                <a class="dashboard-cards-a" href="{{route('tbooke-learning')}}">
                    <div class="card">
                        <div class="card-header learning">
                            <h5 class="card-title mb-3 card-title-dashboard">Tbooke Learning</h5>
                            <p>Explore Tbooke Learning, your hub for diverse educational content, interactive lessons, and meaningful connections with learners and educators.</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6">
				<a class="dashboard-cards-a" href="{{route('learning-resources')}}">
                <div class="card">
                    <div class="card-header resources">
                        <h5 class="card-title mb-3 card-title-dashboard">Learning Resources</h5>
                        <p>Discover a variety of educational tools and resources at Tbooke Shop. Enhance your skills and knowledge with resources designed to support your learning journey.</p>
                    </div>
                </div>
			</a>
            </div>
            <div class="col-md-6">
				<a class="dashboard-cards-a" href="{{route('schools-corner')}}">
                <div class="card">
                    <div class="card-header schools">
                        <h5 class="card-title mb-3 card-title-dashboard">Schools Corner</h5>
                        <p>Explore Schools Corner at Tbooke, dedicated pages for educational institutions to create and share content, fostering collaboration and innovation.</p>
                    </div>
                </div>
			</a>
            </div>
            <div class="col-md-6">
				<a class="dashboard-cards-a" href="{{route('tbooke-blueboard')}}">
                <div class="card">
                    <div class="card-header  blueboard">
                        <h5 class="card-title mb-3 card-title-dashboard">Tbooke Blueboard</h5>
                        <p>Discover Tbooke's Blueboard, a moderated platform for education-related communications and announcements, connecting educators and learners.</p>
                    </div>
                </div>
			</a>
            </div>
        </div>
    </main>
    {{-- footer --}}
    @include('includes.footer')
</div>
