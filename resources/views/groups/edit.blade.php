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
            <div class="row justify-content-around">
                <div class="col-md-9 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Edit Group</h5>
                        </div>
                        <div class="card-body content-creation-form">
                            <form method="POST" action="{{ route('groups.update', $group->slug) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <input type="text" class="form-control" name="name" id="name" placeholder="Group Name" value="{{ old('name', $group->name) }}" required>
                                </div>
                                <div class="mb-3">
                                    <textarea name="description" id="description" placeholder="Enter Description" class="form-control" required>{{ old('description', $group->description) }}</textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="thumbnail" class="form-label">Group Thumbnail</label>
                                    @if($group->thumbnail)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $group->thumbnail) }}" alt="Current Thumbnail" class="img-fluid" style="max-height: 150px; object-fit: cover;">
                                        </div>
                                    @else
                                        <p>No current thumbnail available.</p>
                                    @endif
                                    <input id="thumbnail" name="thumbnail" type="file" class="form-control mb-3">
                                </div>
                                
                                <div class="mb-3">
                                    <input type="submit" class="btn btn-primary" value="Update" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    {{-- Footer --}}
    @include('includes.footer')
</div>
