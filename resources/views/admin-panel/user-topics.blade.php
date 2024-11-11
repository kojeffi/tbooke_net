@include('includes.admin-header')
{{-- Sidebar --}}
@include('includes.admin-sidebar')
@include('sweetalert::alert')
{{-- Topbar --}}
<div class="main">
    @include('includes.admin-topbar')
    {{-- Main Content --}}
    <main class="content">
        <div class="container-fluid p-0">
            <h1 class="h3 mb-3">Topics</h1>
        </div>

        <div class="row">
            <div class="col-8">
                <div class="card flex-fill">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="card-title mb-0">All Topics</h5>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTopicModal">Add New Topic</button>
                    </div>
                    <table class="table table-hover my-0">
                        <thead>
                            <tr>
                                <th>Topic</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topics as $topic)
                            <tr>
                                <td>{{ $topic->name }}</td>
                                <td>
                                    <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#editTopicModal-{{ $topic->id }}">Edit</button>
                                    <button class="btn btn-danger" onclick="confirmDelete({{ $topic->id }})">Delete</button>
                                    <form id="delete-form-{{ $topic->id }}" action="{{ route('topics.destroy', $topic->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Topic Modal -->
                            <div class="modal fade" id="editTopicModal-{{ $topic->id }}" tabindex="-1" aria-labelledby="editTopicModalLabel-{{ $topic->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editTopicModalLabel-{{ $topic->id }}">Edit Topic</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('topics.update', $topic->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="form-group">
                                                    <label for="editTopicName-{{ $topic->id }}">Topic Name</label>
                                                    <input type="text" name="name" class="form-control" id="editTopicName-{{ $topic->id }}" value="{{ $topic->name }}" required>
                                                </div>
                                                <button type="submit" class="btn btn-primary mt-3">Update Topic</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col mt-0">
                                <h5 class="card-title">Topics</h5>
                            </div>

                            <div class="col-auto">
                                <div class="stat text-primary">
                                    <i class="align-middle" data-feather="book"></i>
                                </div>
                            </div>
                        </div>
                        <h1 class="mt-1 mb-3">{{ $topics->count() }}</h1>
                        <div class="mb-0">
                            <span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i> {{ round($chosenTopicsPercentage, 2) }}% </span>
                            <span class="text-muted">Topics chosen by users</span>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col mt-0">
                                <h5 class="card-title">Most Popular Topic</h5>
                            </div>

                            <div class="col-auto">
                                <div class="stat text-primary">
                                    <i class="align-middle" data-feather="star"></i>
                                </div>
                            </div>
                        </div>
                        <h1 class="mt-1 mb-3">{{ $mostPopularTopicName }}</h1>
                        <div class="mb-0">
                            <span class="text-info"> <i class="mdi mdi-account"></i> {{ $mostPopularTopicCount }} users </span>
                            <span class="text-muted">have chosen this topic</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    {{-- footer --}}
    @include('includes.footer')
</div>

<!-- Add Topic Modal -->
<div class="modal fade" id="addTopicModal" tabindex="-1" aria-labelledby="addTopicModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTopicModalLabel">Add New Topic</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('topics.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="addTopicName">Topic Name</label>
                        <input type="text" name="name" class="form-control" id="addTopicName" required>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Add Topic</button>
                </form>
            </div>
        </div>
    </div>
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
