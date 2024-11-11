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
            <h1 class="h3 mb-3">Subjects</h1>
        </div>

        <div class="row">
            <div class="col-8">
                <div class="card flex-fill">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="card-title mb-0">All Subjects</h5>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubjectModal">Add New Subject</button>
                    </div>
                    <table class="table table-hover my-0">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subjects as $subject)
                            <tr>
                                <td>{{ $subject->name }}</td>
                                <td>
                                    <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#editSubjectModal-{{ $subject->id }}">Edit</button>
                                    <button class="btn btn-danger" onclick="confirmDelete({{ $subject->id }})">Delete</button>
                                    <form id="delete-form-{{ $subject->id }}" action="{{ route('subjects.destroy', $subject->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Subject Modal -->
                            <div class="modal fade" id="editSubjectModal-{{ $subject->id }}" tabindex="-1" aria-labelledby="editSubjectModalLabel-{{ $subject->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editSubjectModalLabel-{{ $subject->id }}">Edit Subject</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('subjects.update', $subject->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="form-group">
                                                    <label for="editSubjectName-{{ $subject->id }}">Subject Name</label>
                                                    <input type="text" name="name" class="form-control" id="editSubjectName-{{ $subject->id }}" value="{{ $subject->name }}" required>
                                                </div>
                                                <button type="submit" class="btn btn-primary mt-3">Update Subject</button>
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
                                <h5 class="card-title">Subjects</h5>
                            </div>

                            <div class="col-auto">
                                <div class="stat text-primary">
                                    <i class="align-middle" data-feather="book"></i>
                                </div>
                            </div>
                        </div>
                        <h1 class="mt-1 mb-3">{{ $subjects->count() }}</h1>
                        <div class="mb-0">
                            <span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i> {{ round($chosenSubjectsPercentage, 2) }}% </span>
                            <span class="text-muted">Subjects chosen by users</span>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col mt-0">
                                <h5 class="card-title">Most Popular Subject</h5>
                            </div>

                            <div class="col-auto">
                                <div class="stat text-primary">
                                    <i class="align-middle" data-feather="star"></i>
                                </div>
                            </div>
                        </div>
                        <h1 class="mt-1 mb-3">{{ $mostPopularSubjectName }}</h1>
                        <div class="mb-0">
                            <span class="text-info"> <i class="mdi mdi-account"></i> {{ $mostPopularSubjectCount }} users </span>
                            <span class="text-muted">have chosen this subject</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    {{-- footer --}}
    @include('includes.footer')
</div>

<!-- Add Subject Modal -->
<div class="modal fade" id="addSubjectModal" tabindex="-1" aria-labelledby="addSubjectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSubjectModalLabel">Add New Subject</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('subjects.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="addSubjectName">Subject Name</label>
                        <input type="text" name="name" class="form-control" id="addSubjectName" required>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Add Subject</button>
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