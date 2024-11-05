@include('includes.admin-header')
@include('includes.admin-sidebar')
@include('sweetalert::alert')

<div class="main">
    @include('includes.admin-topbar')
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-md-12 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Conversation on: {{ $message->subject }}</h5>
                        </div>
                        <div class="card-body">
                            <!-- Initial Message -->
                            <div class="mb-4">
                                <h6><strong>Initial Message:</strong></h6>
                                <p>{{ $message->message }}</p>
                                <small>Sent by: <span class="text-blue"><b>
                                @if ($message->user->profile_type == "institution")
                                    {{ $message->user->institutionDetails->institution_name }}
                                    @else
                                    {{ $message->user->first_name }} {{ $message->user->surname }}
                                @endif
                                </b></span> on {{ $message->created_at->format('d-m-Y H:i') }}</small>
                            </div>
                            
                            <hr/>

                            <!-- Replies -->
                   <div class="mb-4">
                        <h6><strong>Replies:</strong></h6>
                        @forelse($message->replies as $reply)
                            <div class="mb-3 p-3" style="background-color: {{ $reply->admin_id ? '#f1f1f1' : '#e3f2fd' }}; border-radius: 10px;">
                                <p>{{ $reply->message }}</p>
                                <small>
                                    Sent by: 
                                    @if($reply->admin_id)
                                        <span class="text-blue"><b>Admin</b></span>
                                    @else
                                        <span class="text-blue"><b>
                                            @if ($reply->user->profile_type == "institution")
                                                {{ $reply->user->institutionDetails->institution_name }}
                                                @else
                                                {{ $reply->user->first_name }} {{ $reply->user->surname }}
                                            @endif
                                        </b></span>
                                    @endif
                                    on {{ $reply->created_at->format('d-m-Y H:i') }}
                                </small>
                            </div>
                        @empty
                            <p>No replies yet.</p>
                        @endforelse
                    </div>


                            <hr/>

                            <!-- Reply Form -->
                            <form action="{{ route('admin.messages.storeAdminReply', $message->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="message" class="form-label"><strong>Reply:</strong></label>
                                    <textarea name="message" id="message" class="form-control" rows="4" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Send Reply</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    @include('includes.footer')
</div>
