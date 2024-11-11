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
                            <h5 class="card-title mb-0">Edit School</h5>
                        </div>
                        <div class="card-body content-creation-form">
                        <form method="POST" action="{{ route('schools-corner.update', $school->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                    
                            <div class="mb-3">
                                <input type="text" class="form-control" name="name" placeholder="School name" value="{{ $school->name }}" required>
                            </div>
                            
                        
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" name="description" placeholder="Describe your school..." rows="5" required>{{ $school->description }}</textarea>
                            </div>
                            
                        
                            <div class="mb-3">
                                <label for="image" class="form-label">School Logo</label><br>
                                
                                @if($school->thumbnail)
                                    <img src="{{ asset('storage/' . $school->thumbnail) }}" alt="School Image" class="img-thumbnail mb-2" width="150">
                                @endif
                                <input id="image" name="image" type="file" class="form-control mb-3">
                            </div>
                            
                            <!-- Submit Button -->
                            <div class="mb-3">
                                <input type="submit" class="btn btn-primary" value="Submit">
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
