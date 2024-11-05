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
            <div class="row">
                <div class="col-md-4 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Messages</h5>
                        </div>
                        <div class="list-group list-group-flush">
                            @foreach($allUsers as $user)
                                <a href="{{ route('messages.show', $user->username) }}" 
                                class="list-group-item list-group-item-action d-flex align-items-start {{ $user->backgroundClass }}">
                                    @if ($user->profile_picture)
                                        <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile Picture" class="img-fluid rounded-circle me-2" width="50" height="50">
                                    @else
                                        <img src="{{ asset('/default-images/avatar.png') }}" alt="Default Profile Picture" class="img-fluid rounded-circle me-2" width="50" height="50">
                                    @endif
                                    <div>
                                        <div class="font-weight-bold">{{ $user->first_name }} {{ $user->surname }}</div>
                                        <div class="text-muted small">{{ Str::limit(optional($user->lastMessage)->content, 30) }}</div>
                                        @if ($user->unreadMessageCount > 0)
                                            <span class="badge bg-danger">{{ $user->unreadMessageCount }}</span>
                                        @endif
                                    </div>
                                </a>
                            @endforeach

                        </div>
                    </div>
                </div>
                <div class="col-md-8 col-12">
                    @if ($recipient)
                        <div class="card">
                            <div class="card-header d-flex align-items-center">
                                @if ($recipient->profile_picture)
                                    <img src="{{ asset('storage/' . $recipient->profile_picture) }}" alt="Profile Picture" class="img-fluid rounded-circle me-2" width="50" height="50">
                                @else
                                    <img src="{{ asset('/default-images/avatar.png') }}" alt="Default Profile Picture" class="img-fluid rounded-circle me-2" width="50" height="50">
                                @endif
                                <div>
                                    <h5 class="card-title mb-0">{{ $recipient->first_name }} {{ $recipient->surname }}</h5>
                                    <div class="text-muted mb-2 capitalize">{{ $recipient->profile_type }}</div>
                                </div>
                            </div>
                            <div class="card-body">
                                {{-- Display previous messages --}}
                                <div class="mb-3">
                                   @foreach($messages as $message)
                                        <div class="d-flex {{ $message->sender_id == Auth::id() ? 'justify-content-end' : '' }} mb-2">
                                            <div class="{{ $message->sender_id == Auth::id() ? 'text-end' : '' }}">
                                                <div class="p-2 rounded {{ $message->sender_id == Auth::id() ? 'bg-primary text-white' : 'bg-light' }}">
                                                    {{ $message->content }}
                                                </div>
                                                <div class="text-muted small">{{ $message->created_at->format('M d, Y, g:i A') }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Send new message form --}}
                                <form method="POST" action="{{ route('messages.store') }}">
                                    @csrf
                                    <input type="hidden" name="receiver_id" value="{{ $recipient->id }}">
                                    <div class="mb-3">
                                        <textarea class="form-control" name="content" placeholder="Type your message..." rows="3"></textarea>
                                    </div>
                                    <div class="mb-3 text-end">
                                        <button type="submit" class="btn btn-primary">Send</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="card">
                            <div class="card-body">
                                <p>Select a user to view messages</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>
    {{-- footer --}}
    @include('includes.footer')
</div>