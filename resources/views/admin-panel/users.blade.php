@include('includes.admin-header')
{{-- Sidebar --}}
@include('includes.admin-sidebar')

{{-- Topbar --}}
<div class="main">
    @include('includes.admin-topbar')
    {{-- Main Content --}}
    <main class="content">
        <div class="container-fluid p-0">
            <h1 class="h3 mb-3">Dashboard</h1>

            {{-- Users Table --}}
            <div class="row">
                <div class="col-12 col-lg-12 col-xxl-9">
                    <div class="card flex-fill">
                 <div class="card-header">
                    <h5 class="card-title mb-0">Users</h5>
                    <div class="filter-container">
                        <h6>Filter Table:</h6>
                        <label><input type="checkbox" name="profile_type" class="profile-type-filter" value="student"> Students</label>
                        <label><input type="checkbox" name="profile_type" class="profile-type-filter" value="teacher"> Teachers</label>
                        <label><input type="checkbox" name="profile_type" class="profile-type-filter" value="institution"> Institutions</label>
                        <label><input type="checkbox" name="profile_type" class="profile-type-filter" value="other"> Others</label>
                        <label><input type="checkbox" id="archived-filter"> Archived</label>
                    </div>
                </div>

                        <table class="table table-hover my-0">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Full Name</th>
                                    <th class="d-none d-xl-table-cell">Email</th>
                                    <th class="d-none d-xl-table-cell">Type</th>
                                    <th class="d-none d-md-table-cell">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                <tr class="{{ $user->deleted_at ? 'archived' : '' }}" style="{{ $user->deleted_at ? 'background-color: #f8d7da;' : '' }}">
                                    <td>
                                        @if ($user->profile_picture)
                                            <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile Picture" class="rounded-circle img-fluid me-2" width="36" height="36">
                                        @else
                                            <img src="{{ asset('/default-images/avatar.png') }}" alt="Default Profile Picture" class="rounded-circle img-fluid me-2" width="36" height="36">
                                        @endif
                                    </td>
                                    <td>
                                        @if ($user->profile_type == 'institution' && $user->institutionDetails)
                                            {{ $user->institutionDetails->institution_name }}
                                        @else
                                            {{ $user->first_name }} {{ $user->surname }}
                                        @endif
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->profile_type }}</td>
                                    <td>
                                        @if ($user->deleted_at)
                                        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#editUserModal" 
                                            onclick="openEditModal('{{ $user->id }}', '{{ $user->first_name }}', '{{ $user->surname }}', '{{ $user->email }}')" disabled>
                                            Edit
                                        </button>
                                        @else
                                        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#editUserModal" 
                                            onclick="openEditModal('{{ $user->id }}', '{{ $user->first_name }}', '{{ $user->surname }}', '{{ $user->email }}')">
                                            Edit
                                        </button>    
                                        @endif
                                        @if ($user->deleted_at)
                                            <button class="btn btn-danger" onclick="unarchiveUser('{{ $user->id }}')">Unarchive</button>
                                        @else
                                            <button class="btn btn-danger" onclick="confirmArchive('{{ $user->id }}')">Archive</button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{-- Pagination links --}}
                        <div class="d-flex justify-content-between mt-3">
                            <div class="pagination-ul-div">
                                {{ $users->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- footer --}}
    @include('includes.footer')
</div>

{{-- Edit User Modal --}}
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="editUserId">
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="editFirstName" name="first_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="surname" class="form-label">Surname</label>
                        <input type="text" class="form-control" id="editSurname" name="surname" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openEditModal(id, firstName, surname, email) {
        var formAction = "/admin/users/" + id + "/update"; // Adjust URL according to your routes
        document.getElementById('editUserForm').action = formAction;
        document.getElementById('editUserId').value = id;
        document.getElementById('editFirstName').value = firstName;
        document.getElementById('editSurname').value = surname;
        document.getElementById('editEmail').value = email;
    }

    // SweetAlert for archive confirmation
    function confirmArchive(userId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to fully delete the user, but the user will be archived!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, archive it!'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.action = '/admin/users/' + userId + '/archive';
                form.method = 'POST';
                form.innerHTML = `@csrf <input type="hidden" name="_method" value="POST">`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function unarchiveUser(userId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This user will be unarchived!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, unarchive it!'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.action = '/admin/users/' + userId + '/unarchive';
                form.method = 'POST';
                form.innerHTML = `@csrf <input type="hidden" name="_method" value="POST">`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Filter Logic
    document.querySelectorAll('.profile-type-filter, #archived-filter').forEach(filter => {
    filter.addEventListener('change', () => {
        const selectedTypes = Array.from(document.querySelectorAll('.profile-type-filter:checked')).map(el => el.value);
        const showArchived = document.getElementById('archived-filter').checked;

        document.querySelectorAll('tbody tr').forEach(row => {
            const profileType = row.querySelector('td:nth-child(4)').innerText.trim();
            const isArchived = row.classList.contains('archived');

            const typeMatches = selectedTypes.length === 0 || selectedTypes.includes(profileType);
            const archivedMatches = !showArchived || isArchived;

            if (typeMatches && archivedMatches) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});

</script>