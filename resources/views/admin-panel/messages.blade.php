@include('includes.admin-header')
@include('includes.admin-sidebar')
@include('sweetalert::alert')

<div class="main">
    @include('includes.admin-topbar')
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row">
                {{-- Left Panel: List of Users --}}
                <div class="col-md-12 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">User Messages</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>Subject</th>
                                            <th>Message</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($initialMessages as $message)
                                            <tr>
                                                <td>
                                                    @if ($message->user->profile_type == 'institution')
                                                        {{ $message->user->institutionDetails->institution_name }}
                                                        @else
                                                        {{ $message->user->first_name }} {{ $message->user->surname }}
                                                    @endif
                                                    @if($message->totalUnreadCount > 0)
                                                        <span class="badge bg-danger ms-2">{{ $message->totalUnreadCount }} new</span>
                                                    @endif
                                                </td>
                                                <td>{{ $message->subject }}</td>
                                                <td>{{ \Illuminate\Support\Str::limit($message->message, 10) }}</td> 
                                                <td>{{ $message->created_at->format('Y-m-d H:i') }}</td>
                                                <td>
                                                    <a href="{{ route('admin-panel.show', $message->id) }}" class="btn btn-primary btn-sm">View</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    @include('includes.footer')
</div>