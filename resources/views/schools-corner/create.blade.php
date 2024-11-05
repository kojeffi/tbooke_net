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
                            <h5 class="card-title mb-0">Add Your School</h5>
                        </div>
                        <div class="card-body content-creation-form">
                            <form method="POST" action="{{ route('schools.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <input type="text" class="form-control" name="name" placeholder="School name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" name="description" placeholder="Describe your school..." rows="5" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="image" class="form-label">School Image or Logo</label>
                                    <input id="image" name="image" type="file" class="form-control mb-3" required>
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
