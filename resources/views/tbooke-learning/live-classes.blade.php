@include('includes.header')
@include('includes.sidebar')
@include('sweetalert::alert')

<div class="main">
    @include('includes.topbar')
    <main class="content">
        <div class="container-fluid p-0">
            <!-- Live Classes Section -->
            <div class="row">
                <div class="col-md-12">
                    <h1 class="h3">Live Classes</h1>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Class Name</th>
                                    <th>Creator</th>
                                    <th>Countdown</th>
                                    <th>Time</th>
                                    <th>Registered</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($liveClasses->sortByDesc('created_at') as $liveClass)
                                    <tr>
                                        <td>{{ $liveClass->class_name }}</td>
                                        <td>{{ $liveClass->creator_name }}</td>
                                        <td>
                                            @if($liveClass->isOngoing())
                                                <span class="badge bg-success">Class Ongoing</span>
                                            @elseif($liveClass->hasEnded())
                                                <span class="badge bg-danger">Class Ended</span>
                                            @else
                                                <div id="countdown-{{ $liveClass->id }}" class="countdown" data-start-date="{{ $liveClass->class_date }} {{ $liveClass->class_time }}"></div>
                                            @endif
                                        </td>
                                        <td>{{ date('H:i', strtotime($liveClass->class_time)) }}</td>
                                        <td>{{ $liveClass->registration_count }}</td>
                                        <td>
                                          @auth
                                                @if($liveClass->isOngoing() && $liveClass->users->contains(Auth::user()))
                                                    <a href="{{ route('live-classes.show', $liveClass->slug) }}" class="btn btn-success">Join</a>
                                                @elseif($liveClass->hasEnded())
                                                    <a href="{{ route('live-classes.show', $liveClass->slug) }}" class="btn btn-info">View</a>
                                                @else
                                                    @if($liveClass->users->contains(Auth::user()))
                                                        <button class="btn btn-secondary" disabled>Registered</button>
                                                    @else
                                                        <form action="{{ route('live-classes.register', $liveClass->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-primary">Register</button>
                                                        </form>
                                                    @endif
                                                @endif
                                            @endauth
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    @include('includes.footer')
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const countdownElements = document.querySelectorAll('.countdown');
        countdownElements.forEach(element => {
            const startDate = new Date(element.getAttribute('data-start-date')).getTime();
            const interval = setInterval(() => {
                const now = new Date().getTime();
                const distance = startDate - now;
                
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                element.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;

                if (distance < 0) {
                    clearInterval(interval);
                    element.innerHTML = "Class Ongoing";
                    element.classList.add('badge');
                    element.classList.add('bg-success');
                }
            }, 1000);
        });
    });
</script>
