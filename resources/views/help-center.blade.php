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
            <h1 class="h3 mb-3">Help Center</h1>
            
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Contact Tbooke Admin</h5>
                        </div>
                        <div class="card-body">
                            <p>If you have any issues or need assistance, please feel free to reach out to our support team. We are here to help you with any concerns or questions you might have.</p>

                            {{-- Link to Contact Admin Page --}}
                            <a href="{{ route('admin-messages.index') }}" class="btn btn-primary">Contact Admin</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    {{-- footer --}}
    @include('includes.footer')
</div>
