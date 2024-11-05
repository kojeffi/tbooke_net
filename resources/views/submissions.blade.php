@section('content')

    <h1>Form Submissions</h1>
    <ul>
        @foreach($submissions as $submission)
            <li>
                <strong>School Name:</strong> {{ $submission->school_name }}<br>

                <strong>Advertisement:</strong> {{ $submission->advertisement }}<br>

                <strong>Image:</strong> <img src="{{ asset($submission->image) }}" alt="Image"><br>

            </li>
        @endforeach
    </ul>

@endsection

