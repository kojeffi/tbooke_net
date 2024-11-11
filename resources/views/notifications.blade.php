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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Notifications</h1>
            </div>
            <div class="row content">
                <div class="col-md-12">
                    @if($notificationspage->isEmpty())
                        <p>You have no notifications.</p>
                    @else
                        <ul class="list-group">
                            @foreach($notificationspage as $notification)
                                @if($notification->type === 'New Connection')
                                    <li class="list-group-item {{ $notification->read ? '' : 'font-weight-bold' }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <a href="{{ route('profile.show', ['username' => $notification->sender->username]) }}">
                                                    @if ($notification->sender->profile_picture)
                                                        <img src="{{ asset('storage/' . $notification->sender->profile_picture) }}" alt="Profile Picture" class="img-fluid rounded-circle me-2" width="50" height="50">
                                                    @else
                                                        <img src="{{ asset('/default-images/avatar.png') }}" alt="Default Profile Picture" class="img-fluid rounded-circle me-2" width="50" height="50">
                                                    @endif
                                                    @if ($notification->sender->profile_type === 'institution')
                                                        <span>{{ $notification->sender->institutionDetails->institution_name }}</span>
                                                        @else
                                                        <span>{{ $notification->sender->first_name }} {{ $notification->sender->surname }}</span>
                                                    @endif
                                                </a>
                                                <p class="mb-0 ms-2">has connected with you.</p>
                                            </div>
                                            <div>
                                                <small>{{ $notification->created_at->diffForHumans() }}</small>
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-danger btn-sm delete-notification" data-id="{{ $notification->id }}">Delete</button>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </main>

    <script>
        const notificationDeleteRoute = "{{ route('notifications.destroy', ':id') }}";
    </script>
    {{-- footer --}}
    @include('includes.footer')
</div>