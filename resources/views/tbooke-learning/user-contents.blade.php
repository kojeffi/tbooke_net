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
                    <h1 class="h3">My Content</h1>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contents as $content)
                                    <tr>
                                        <td><a href="{{ route('content.show', $content->slug) }}">{{ $content->content_title }}</a></td>
                                        <td>{{ $content->created_at->format('F j, Y') }}</td>
                                        <td>
                                            <a href="{{ route('tbooke-learning.edit', ['username' => $content->user->username, 'id' => $content->id]) }}" class="btn btn-warning">Edit</a>
                                            <form id="delete-form-{{ $content->id }}" method="POST" action="{{ route('tbooke-learning.delete', ['username' => $content->user->username, 'id' => $content->id]) }}" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger delete-button" data-username="{{ $content->user->username }}" data-id="{{ $content->id }}">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No content found.</td>
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
    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent default form submission
            
            const username = this.getAttribute('data-username'); // Get username from data attribute
            const id = this.getAttribute('data-id'); // Get id from data attribute

            // Construct the form action URL using the username and id
            const form = this.closest('form');
            form.action = `{{ url('tbooke-learning/creator') }}/${username}/delete/${id}`;

            // Show SweetAlert confirmation dialog
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
                    form.submit(); // Submit the form if confirmed
                }
            });
        });
    });
</script>