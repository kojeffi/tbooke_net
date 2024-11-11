@include('includes.header')
{{-- Sidebar --}}
@include('includes.sidebar')

{{-- Topbar --}}
<div class="main">
    @include('includes.topbar')
    {{-- Main Content --}}
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row justify-content-around">
                <div class="col-md-9 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ isset($post) ? 'Edit Blueboard Post' : 'Create Blueboard Post' }}</h5>
                        </div>
                        <div class="card-body content-creation-form">
                           <form method="POST" action="{{ isset($post) ? route('blueboard.update', ['username' => $post->user->username, 'id' => $post->id]) : route('blueboard.store') }}">
                                @csrf
                                @if (isset($post))
                                    @method('PUT') <!-- Use PUT method for updating -->
                                @endif
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" placeholder="Enter title" value="{{ isset($post) ? $post->title : '' }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="content" class="form-label">Content</label>
                                    <textarea class="form-control" id="content" name="content" rows="5" placeholder="Enter content" required>{{ isset($post) ? $post->content : '' }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">{{ isset($post) ? 'Update' : 'Submit' }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    {{-- footer --}}
    @include('includes.footer')
</div>

