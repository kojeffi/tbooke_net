@include('includes.header')
@include('includes.sidebar')
@include('sweetalert::alert')

<div class="main">
    @include('includes.topbar')
    <main class="content">
        <div class="container-fluid p-0">
            <!-- User Content Section -->
            <div class="row">
                <div class="col-md-12">
                    <h1 class="h3">My Schools</h1>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>School Name</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($schools as $school)
                                    <tr>
                                        <td><a href="{{ route('schools.show', $school->slug) }}">{{ $school->name }}</a></td>
                                        <td>{{ $school->created_at->format('F j, Y') }}</td>
                                        <td>
                                            <a href="{{ route('schools-corner.edit', $school->id) }}" class="btn btn-warning">Edit</a>
                                             <form id="delete-form-{{ $school->id }}" method="POST" action="{{ route('schools-corner.deleteSchool', ['id' => $school->id]) }}" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger delete-button" data-id="{{ $school->id }}">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No schools found.</td>
                                    </tr>
                                @endforelse
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
    document.addEventListener('DOMContentLoaded', function () {
    const deleteButtons = document.querySelectorAll('.delete-button');

    deleteButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const schoolId = button.getAttribute('data-id');
            const form = document.getElementById(`delete-form-${schoolId}`);

            Swal.fire({
                title: 'Are you sure?',
                text: 'You won’t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>