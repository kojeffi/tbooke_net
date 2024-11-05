@include('includes.header')
{{-- Sidebar --}}
@include('includes.sidebar')
@include('sweetalert::alert')

{{-- Topbar --}}
<div class="main"> {{-- Removed global bg-dark --}}
    @include('includes.topbar')
    {{-- Main Content --}}
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row">
                {{-- Left Panel: List of Previous Messages --}}
                <div class="col-md-4 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0" style="padding-bottom: 1rem;">
                                Admin Messages
                            </h5>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newMessageModal">New Message</button>
                        </div>
                        <div class="list-group list-group-flush">
                            @foreach($messages as $msg)
                                {{-- Apply bg-dark and show reply count for unread messages only --}}
                                <a href="{{ route('admin-messages.show', $msg->id) }}" 
                                    class="list-group-item list-group-item-action {{ $unreadMessagerepliesCount ? 'bg-dark text-white' : '' }}">
                                    <div>
                                        <div class="font-weight-bold">{{ $msg->subject }}   
                                        @if ($unreadMessagerepliesCount > 0)
                                            <span class="badge bg-danger">{{ $unreadMessagerepliesCount }} new</span>
                                        @endif
                                        </div>
                                        <div class="text-muted small">{{ Str::limit(strip_tags($msg->message), 50) }}
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Right Panel: Display the Conversation & Message Form --}}
                <div class="col-md-8 col-12">
                    @if ($currentMessage)
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">{{ $currentMessage->subject }}</h5>
                            </div>
                            <div class="card-body">
                                {{-- Display the initial message --}}
                                <div class="mb-3">
                                    <div class="d-flex {{ $currentMessage->user_id == Auth::id() ? 'justify-content-end' : '' }} mb-2">
                                        <div class="{{ $currentMessage->user_id == Auth::id() ? 'text-end' : '' }}">
                                            <div class="p-2 rounded {{ $currentMessage->user_id == Auth::id() ? 'bg-primary text-white' : 'bg-light' }}">
                                                {{ $currentMessage->message }}
                                            </div>
                                            <div class="text-muted small" style="display: block;">{{ $currentMessage->created_at->format('M d, Y, g:i A') }}</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Display previous conversations --}}
                                @foreach($currentMessage->replies as $reply)
                                    <div class="mb-3">
                                        <div class="d-flex {{ !$reply->admin_id ? 'justify-content-end' : '' }} mb-2">
                                            <div class="text-end">
                                                <div class="p-2 rounded {{ !$reply->admin_id ? 'bg-primary text-white' : '' }} {{ $reply->admin_id ? 'bg-light' : ''}}">
                                                    {{ $reply->message }}
                                                </div>
                                                <div class="text-muted small" style="display: block;">{{ $reply->created_at->format('M d, Y, g:i A') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Reply box --}}
                                <form method="POST" action="{{ route('admin-messages.reply', ['message' => $currentMessage->id]) }}">
                                    @csrf
                                    <input type="hidden" name="message_id" value="{{ $currentMessage->id }}">
                                    <div class="mb-3">
                                        <textarea class="form-control" name="message" placeholder="Type your reply..." rows="3"></textarea>
                                    </div>
                                    <div class="mb-3 text-end">
                                        <button type="submit" class="btn btn-primary">Send Reply</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="card">
                            <div class="card-body">
                                <p>Select a message to view the conversation and reply.</p>
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

{{-- Modal for New Message --}}
<div class="modal fade" id="newMessageModal" tabindex="-1" aria-labelledby="newMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newMessageModalLabel">New Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin-messages.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                    </div>
                    <div class="mb-3 text-end">
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
