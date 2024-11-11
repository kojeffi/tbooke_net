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
                <div class="col-md-10 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Create Live Class</h5>
                        </div>

                        <div class="row card-body content-creation-form">
                            <form method="POST" action="{{ route('live-classes.store') }}" enctype="multipart/form-data">
    @csrf
    @method('post')
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <input type="text" class="form-control" name="class_name" placeholder="Topic" value="{{ old('class_name') }}">
                @if($errors->has('class_name'))
                    <div class="text-danger">{{ $errors->first('class_name') }}</div>
                @endif
            </div>
            <div class="mb-3">
                <select class="form-select rounded" id="classCategory" name="class_category">
                    <option selected="" disabled="">Select category</option>
                    <option value="Math" {{ old('class_category') == 'Math' ? 'selected' : '' }}>Math</option>
                    <option value="Science" {{ old('class_category') == 'Science' ? 'selected' : '' }}>Science</option>
                    <option value="Language Arts" {{ old('class_category') == 'Language Arts' ? 'selected' : '' }}>Language Arts</option>
                    <option value="History" {{ old('class_category') == 'History' ? 'selected' : '' }}>History</option>
                    <option value="Technology" {{ old('class_category') == 'Technology' ? 'selected' : '' }}>Technology</option>
                    <option value="Arts" {{ old('class_category') == 'Arts' ? 'selected' : '' }}>Arts</option>
                    <option value="Physical Education" {{ old('class_category') == 'Physical Education' ? 'selected' : '' }}>Physical Education</option>
                    <option value="Other" {{ old('class_category') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                @if($errors->has('class_category'))
                    <div class="text-danger">{{ $errors->first('class_category') }}</div>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <input type="date" class="form-control" name="class_date" value="{{ old('class_date') }}">
                @if($errors->has('class_date'))
                    <div class="text-danger">{{ $errors->first('class_date') }}</div>
                @endif
            </div>
         <div class="mb-3">
    <input type="text" class="form-control" id="classTime" name="class_time" value="{{ old('class_time') }}" placeholder="Select time">
    @if($errors->has('class_time'))
        <div class="text-danger">{{ $errors->first('class_time') }}</div>
    @endif
</div>

        </div>
        <div class="col-md-12">
            <div class="mb-3">
                <textarea class="form-control" name="class_description" placeholder="Start typing class description..." rows="5">{{ old('class_description') }}</textarea>
                @if($errors->has('class_description'))
                    <div class="text-danger">{{ $errors->first('class_description') }}</div>
                @endif
            </div>
        </div>
        <div class="mb-3">
            <input type="submit" class="btn btn-primary" value="Create Class" />
        </div>
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
