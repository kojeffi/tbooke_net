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
							<h5 class="card-title mb-0">Create Group</h5>
						</div>
                        <div class="card-body content-creation-form">
                            <form method="POST" action="{{ route('groups.store') }}" enctype="multipart/form-data">
                            @csrf
                            @method('post')
                            
                                <div class="mb-3">
                                    <input type="text" class="form-control" name="name" id="name" placeholder="Group Name" required>
                                </div>
                                <div class="mb-3">
                                     <textarea name="description" id="description" placeholder="Enter Description" class="form-control" required></textarea>
                                </div>
                                <div class="mb-3">
									<label for="thumbnail" class="form-label">Group Thumbnail</label>
									<input id="thumbnail" name="thumbnail" type="file" class="form-control mb-3">
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
    {{-- footer --}}
    @include('includes.footer')
</div>