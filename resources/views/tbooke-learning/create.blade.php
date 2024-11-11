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
                            <h5 class="card-title mb-0">Create Content</h5>
                        </div>
                        <div class="card-body content-creation-form">
                            <form method="POST" action="{{ route('tbooke-learning.store') }}" enctype="multipart/form-data">
                                @csrf
                                @method('post')

                                <div class="mb-3">
                                    <input type="text" class="form-control @error('content_title') is-invalid @enderror" name="content_title" placeholder="Content title" value="{{ old('content_title') }}">
                                    @error('content_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                               <div class="mb-3 content-category">
                                    <select data-placeholder="Content category" name="content_category[]" multiple class="chosen-select-width form-select @error('content_category') is-invalid @enderror">
                                        <option value="Pre School" {{ (old('content_category') && in_array('Pre School', old('content_category'))) ? 'selected' : '' }}>Pre School</option>
                                        <option value="Grades 1-6" {{ (old('content_category') && in_array('Grades 1-6', old('content_category'))) ? 'selected' : '' }}>Grades 1-6</option>
                                        <option value="CBC Content" {{ (old('content_category') && in_array('CBC Content', old('content_category'))) ? 'selected' : '' }}>CBC Content</option>
                                        <option value="JSS" {{ (old('content_category') && in_array('JSS', old('content_category'))) ? 'selected' : '' }}>Junior Secondary School</option>
                                        <option value="High School" {{ (old('content_category') && in_array('High School', old('content_category'))) ? 'selected' : '' }}>High School</option>
                                    </select>
                                    @error('content_category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                            <div class="mb-3">
    <label for="content_thumbnail" class="form-label">Content Thumbnail (Optional)</label>
    <input id="content_thumbnail" name="content_thumbnail" type="file" class="form-control mb-3 @error('content_thumbnail') is-invalid @enderror">
    @error('content_thumbnail')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>


                                <div class="mb-3">
                                    <label for="media_files" class="form-label">Upload Media Files</label>
                                    <input id="media_files" name="media_files[]" type="file" class="form-control @error('media_files') is-invalid @enderror" multiple>
                                    @error('media_files')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div id="media-preview" class="mt-3"></div>
                                </div>

                                <div class="mb-3">
                                    <textarea class="form-control tinymce-textarea @error('content') is-invalid @enderror" name="content" placeholder="Start typing your content..." rows="10">{{ old('content') }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <input type="submit" class="btn btn-primary" value="Submit" />
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