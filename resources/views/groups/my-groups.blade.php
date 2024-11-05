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
            <div class="row content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">My Groups</h1>
                    {{-- Button to create a new group --}}
                    <a href="{{ route('group.create') }}" class="btn btn-primary">Create New Group</a>
                </div>

                {{-- Check if the user has any groups --}}
                @if($myGroups->isEmpty())
                    <p>You have not created any groups yet.</p>
                @else
                    {{-- Display list of user's groups --}}
                    <div class="row content">
                        @foreach($myGroups as $group)
                            <div class="col-lg-3 col-md-3 col-sm-6 mb-4">
                                <div class="card text-center groups-card">
                                    {{-- Group thumbnail --}}
                                    <a href="{{ route('groups.show', $group->slug) }}">
                                        <img src="{{ $group->thumbnail ? asset('storage/' . $group->thumbnail) : asset('default-images/group.png') }}" 
                                             alt="{{ $group->name }} thumbnail" 
                                             class="card-img-top img-fluid" 
                                             style="height: 150px; object-fit: cover;">
                                    </a>
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $group->name }}</h5>
                                        <p class="card-text">{{ Str::limit($group->description, 18) }}</p>

                                        {{-- Edit and Delete Buttons --}}
                                        <a href="{{ route('groups.edit', $group->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                        {{-- Delete button --}}
                                        <form id="delete-group-form-{{ $group->slug }}" action="{{ route('groups.destroy', $group->slug) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete('{{ $group->slug }}')">Delete</button>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </main>

<script>
    function confirmDelete(slug) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the form
                document.getElementById('delete-group-form-' + slug).submit();
            }
        })
    }
</script>

    @include('includes.footer')
</div>