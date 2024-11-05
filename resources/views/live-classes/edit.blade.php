@include('includes.header')
@include('includes.sidebar')
@include('sweetalert::alert')

<div class="main">
    @include('includes.topbar')
    <main class="content">
        <div class="container-fluid p-0">
            <h1 class="h3 mb-3">Edit Live Class</h1>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="{{ route('live-classes.update', $liveClass->id) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="className">Class Name</label>
                                            <input type="text" class="form-control" id="className" name="class_name" value="{{ old('class_name', $liveClass->class_name) }}">
                                            @if($errors->has('class_name'))
                                                <div class="text-danger">{{ $errors->first('class_name') }}</div>
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <label for="classCategory">Class Category</label>
                                            <select class="form-select" id="classCategory" name="class_category">
                                                <option selected disabled>Select category</option>
                                                <option value="Math" {{ old('class_category', $liveClass->class_category) == 'Math' ? 'selected' : '' }}>Math</option>
                                                <option value="Science" {{ old('class_category', $liveClass->class_category) == 'Science' ? 'selected' : '' }}>Science</option>
                                                <option value="Language Arts" {{ old('class_category', $liveClass->class_category) == 'Language Arts' ? 'selected' : '' }}>Language Arts</option>
                                                <option value="History" {{ old('class_category', $liveClass->class_category) == 'History' ? 'selected' : '' }}>History</option>
                                                <option value="Technology" {{ old('class_category', $liveClass->class_category) == 'Technology' ? 'selected' : '' }}>Technology</option>
                                                <option value="Arts" {{ old('class_category', $liveClass->class_category) == 'Arts' ? 'selected' : '' }}>Arts</option>
                                                <option value="Physical Education" {{ old('class_category', $liveClass->class_category) == 'Physical Education' ? 'selected' : '' }}>Physical Education</option>
                                                <option value="Other" {{ old('class_category', $liveClass->class_category) == 'Other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                            @if($errors->has('class_category'))
                                                <div class="text-danger">{{ $errors->first('class_category') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="classDate">Class Date</label>
                                            <input type="date" class="form-control" id="classDate" name="class_date" value="{{ old('class_date', $liveClass->class_date) }}">
                                            @if($errors->has('class_date'))
                                                <div class="text-danger">{{ $errors->first('class_date') }}</div>
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <label for="classTime">Class Time</label>
                                            <input type="time" class="form-control" id="classTime" name="class_time" value="{{ old('class_time', $liveClass->class_time) }}">
                                            @if($errors->has('class_time'))
                                                <div class="text-danger">{{ $errors->first('class_time') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="classDescription">Class Description</label>
                                            <textarea class="form-control" id="classDescription" name="class_description" rows="5">{{ old('class_description', $liveClass->class_description) }}</textarea>
                                            @if($errors->has('class_description'))
                                                <div class="text-danger">{{ $errors->first('class_description') }}</div>
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <button type="submit" class="btn btn-primary">Update Class</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    @include('includes.footer')
</div>
