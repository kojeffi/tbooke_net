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
            <div>
                <div class="row content">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <!-- Left-aligned heading -->
                            <h1 class="h3 mb-0">My Blueboard Posts</h1>
                        </div>
                    </div>
                </div>

                {{-- Display User's Blueboard Posts in a Table --}}
                <div class="row">
                    <div class="col-md-12">
                        @if($posts->isEmpty())
                            <p>No posts available.</p>
                        @else
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Content</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($posts as $post)
                                        <tr>
                                            <td>{{ $post->title }}</td>
                                            <td>{{ Str::limit($post->content, 50) }}</td>
                                            <td>
                                                <a href="{{ route('blueboard.edit', ['username' => $post->user->username, 'id' => $post->id]) }}" class="btn btn-warning btn-sm me-2">Edit</a>
                                                <button class="btn btn-danger btn-sm" onclick="confirmDelete('{{ route('blueboard.delete', $post->id) }}')">Delete</button>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
    {{-- footer --}}
    @include('includes.footer')
</div>
<script>
    function confirmDelete(url) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You will not be able to recover this post!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // If confirmed, submit the form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = '_token';
                input.value = csrfToken;
                form.appendChild(input);
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
