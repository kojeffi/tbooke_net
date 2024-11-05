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
							<h5 class="card-title mb-0">Edit Content</h5>
						</div>
                        <div class="card-body content-creation-form">
                            <form method="POST" action="{{ route('tbooke-learning.update', $content->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- Content Title --}}
                                <div class="mb-3">
                                    <input type="text" class="form-control" name="content_title" value="{{ $content->content_title }}" placeholder="Content title">
                                </div>

                            {{-- Content Category --}}
                                <div class="mb-3 content-category">
                                    <label for="content_category" class="form-label">Content Category</label>
                                    <select name="content_category[]" multiple class="chosen-select-width form-select">
                                        @php
                                            // Convert the comma-separated categories back into an array
                                            $selectedCategories = explode(',', $content->content_category);
                                        @endphp
                                        <option value="Pre School" {{ in_array('Pre School', $selectedCategories) ? 'selected' : '' }}>Pre School</option>
                                        <option value="Grades 1-6" {{ in_array('Grades 1-6', $selectedCategories) ? 'selected' : '' }}>Grades 1-6</option>
                                        <option value="CBC Content" {{ in_array('CBC Content', $selectedCategories) ? 'selected' : '' }}>CBC Content</option>
                                        <option value="JSS" {{ in_array('JSS', $selectedCategories) ? 'selected' : '' }}>Junior Secondary School</option>
                                        <option value="High School" {{ in_array('High School', $selectedCategories) ? 'selected' : '' }}>High School</option>
                                    </select>
                                </div>


                            {{-- Content Thumbnail --}}
                                <div class="mb-3">
									<label for="content_thumbnail" class="form-label">Content Thumbnail</label>
									<input id="content_thumbnail" name="content_thumbnail" type="file" class="form-control mb-3">
                                    @if ($content->content_thumbnail)
                                        <img src="{{ Storage::url($content->content_thumbnail) }}" alt="Current Thumbnail" class="img-thumbnail" style="max-height: 100px;">
                                    @endif
								</div>

                            {{-- Media Files --}}
                                <div class="mb-3">
                                    <label for="media_files" class="form-label">Upload Media Files</label>
                                    <input id="media_files" name="media_files[]" type="file" class="form-control mb-3" multiple>
                                    @if ($content->media_files)
                                        <div id="media-preview" class="mt-3">
                                            @foreach (json_decode($content->media_files, true) as $media)
                                                <img src="{{ Storage::url($media) }}" alt="Media File" class="img-thumbnail" style="max-height: 100px; margin-right: 10px;">
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                            {{-- Content Body --}}
                                <div class="mb-3">
                                     <textarea class="form-control tinymce-textarea" name="content" placeholder="Start typing your content..." rows="10">{{ $content->content }}</textarea>
                                </div>

                            {{-- Submit Button --}}
                                <div class="mb-3">
                                    <input type="submit" class="btn btn-primary" value="Update Content" />
                                </div>

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
