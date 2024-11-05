@include('includes.header')
@include('includes.sidebar')
@include('sweetalert::alert')

<div class="main">
    @include('includes.topbar')
    <main class="content">
        <div class="container-fluid p-0">
         <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h3 mb-3">Classes</h1>
                <a href="{{ route('live-classes.create') }}" class="btn btn-primary">Create Live Class</a>
        </div>
            <div class="row">
                <div class="col-md-12">

                    {{-- Table for classes created by the authenticated user --}}
                    <h4>Your Classes</h4>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Topic</th>
                                    <th>Category</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Registered</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($creatorClasses->sortByDesc('created_at') as $class)
                                    <tr>
                                        <td style="cursor: pointer" title="{{ $class->class_name }}">
                                            {{ \Illuminate\Support\Str::limit($class->class_name, 10) }}
                                        </td>
                                        <td>{{ $class->class_category }}</td>
                                        <td>{{ \Carbon\Carbon::parse($class->class_date)->format('d-m-y') }}</td>
                                        <td>{{ date('H:i', strtotime($class->class_time)) }}</td>
                                        <td>{{ $class->registration_count }}</td>
                                        <td>
                                            @if($class->isOngoing())
                                                <span class="badge bg-success">Ongoing</span>
                                            @elseif($class->hasEnded())
                                                <span class="badge bg-danger">Ended</span>
                                            @else
                                                <span class="badge bg-warning">Upcoming</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('live-classes.show', $class->slug) }}" class="btn btn-info">View</a>
                                            <a href="{{ route('live-classes.edit', $class->id) }}" class="btn btn-primary">Edit</a>
                                            <button class="btn btn-danger" onclick="confirmDelete({{ $class->id }})">Delete</button>
                                            <form id="delete-form-{{ $class->id }}" action="{{ route('live-classes.destroy', $class->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Table for classes created by other users --}}
                    <h4>Other Classes</h4>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Class Name</th>
                                    <th>Category</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Registered</th>
                                    <th>Creator</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($otherClasses->sortByDesc('created_at') as $class)
                                    <tr>
                                        <td style="cursor: pointer" title="{{ $class->class_name }}">
                                            {{ \Illuminate\Support\Str::limit($class->class_name, 10) }}
                                        </td>
                                        <td>{{ $class->class_category }}</td>
                                        <td>{{ \Carbon\Carbon::parse($class->class_date)->format('d-m-y') }}</td>
                                        <td>{{ date('H:i', strtotime($class->class_time)) }}</td>
                                        <td>{{ $class->registration_count }}</td>
                                     <td>
    @if($class->user->profile_type === 'institution')
        <a href="{{ route('profile.show', $class->user->username) }}">
            {{ $class->user->institutionDetails->institution_name }}
        </a>
    @else
        <a href="{{ $class->user->id === auth()->id() ? route('profile.showOwn') : route('profile.show', $class->user->username) }}">
            {{ $class->creator_name }}
        </a>
    @endif
</td>

                                        <td>
                                            @if($class->isOngoing())
                                                <span class="badge bg-success">Ongoing</span>
                                            @elseif($class->hasEnded())
                                                <span class="badge bg-danger">Ended</span>
                                            @else
                                                <span class="badge bg-warning">Upcoming</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('live-classes.show', $class->slug) }}" class="btn btn-info">View</a>
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
    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>