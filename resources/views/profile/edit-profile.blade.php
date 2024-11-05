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
            <h1 class="h3 mb-3">Edit Profile</h1>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-body text-center">
                                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profile_form">
                                    @csrf
                                    @method('post')
                                    @if ($user->profile_picture)
                                        <img id="profile-image" src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile Picture" class="img-fluid rounded-circle mb-2" width="128" height="128">
                                    @else
                                        <img id="profile-image" src="{{ asset('/default-images/avatar.png') }}" alt="Default Profile Picture" class="img-fluid rounded-circle mb-2" width="128" height="128">
                                    @endif
                                    <!-- Profile Picture Input -->
                                    <div class="profile-picture-div">
                                        <label for="profile_picture" class="form-label">Profile Picture</label>
                                        <input id="profile_picture" name="profile_picture" type="file" class="form-control">
                                    </div>

                                    <div id="cropper-container" style="display: none;">
                                        <img id="cropper-image" style="max-width: 100%;" />
                                        <input type="button" id="crop-image-button" class="btn btn-primary mt-2" value="Crop & Save" />
                                    </div>

                                    <input type="hidden" name="cropped_image" id="cropped_image">

                                    <div class="row user-details-section">
                                        @if (Auth::user()->profile_type == 'institution')
                                           <div class="col-md-6">
                                                <input 
                                                    value="{{ Auth::user()->institutionDetails->institution_name }}" 
                                                    name="institution_name" 
                                                    type="text" 
                                                    class="form-control mb-3" 
                                                />
                                            </div>
                                        @else
                                           <div class="col-md-6">
                                                <input 
                                                    value="{{ Auth::user()->first_name }}" 
                                                    name="first_name"
                                                    type="text" 
                                                    class="form-control mb-3" 
                                                />
                                            </div>
                                        @endif
                                        @if (Auth::user()->profile_type == 'institution')
                                            <div class="col-md-6">
                                                <input 
                                                    value="{{ Auth::user()->institutionDetails->institution_location }}" 
                                                    name="institution_location" 
                                                    type="text" 
                                                    class="form-control mb-3" 
                                                />
                                            </div>
                                        @else
                                            <div class="col-md-6">
                                                <input value="{{ Auth::user()->surname }}" name="surname" type="text" class="form-control mb-3" />
                                            </div>
                                        @endif

                                        @if (Auth::user()->profile_type == 'institution')
                                            <div class="insitution-website">
                            
                                                    <input 
                                                        value="{{ Auth::user()->institutionDetails->institution_website }}" 
                                                        name="institution_website" 
                                                        type="text" 
                                                        class="form-control mb-3" 
                                                    />
                                    
                                            </div>
                                        @endif

                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input value="{{ Auth::user()->profile_type }}" type="text" class="form-control mb-3 capitalize" disabled />
                                        </div>
                                        <div class="col-md-6">
                                            <input value="{{ Auth::user()->email }}" name="email" type="text" class="form-control mb-2" />
                                        </div>
                                    </div>
                            </div>
                        </div>
                        <div class="card-body"></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="h6 card-title">About Me</h5>
                        </div>
                        <div class="card-body">
                            @if (Auth::user()->profile_type == 'institution')
                               <textarea class="form-control" name="institution_about" rows="4" cols="5" placeholder="Tell us more about yourself">{{ Auth::user()->institutionDetails->institution_about }}</textarea>
                                @else
                                <textarea class="form-control" name="about" rows="4" cols="5" placeholder="Tell us more about yourself">{{ $profileDetails->about }}</textarea>
                            @endif
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="h6 card-title">My Subjects</h5>
                        </div>
                        <div class="about-card-inner">
                            <select name="user_subjects[]" data-placeholder="Select your subjects" multiple class="chosen-select-width form-select" tabindex="16">
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->name }}" @if(in_array($subject->name, $userSubjects)) selected @endif>{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="h6 card-title">Favorite Topics</h5>
                        </div>
                        <div class="about-card-inner">
                            <select name="favorite_topics[]" data-placeholder="Select your favorite topics" multiple class="chosen-select-width form-select" tabindex="16">
                                @foreach($topics as $topic)
                                    <option value="{{ $topic->name }}" @if(in_array($topic->name, $favoriteTopics)) selected @endif>{{ $topic->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div style="padding-bottom: 18px;" class="card">
                        <div class="card-header">
                            <h5 class="h6 card-title">Social Media</h5>
                        </div>
                        <div class="card-body row align-items-center card-body-forms">
                            <label for="facebookInput" class="col-sm-3 col-form-label">Facebook:</label>
                            <div class="col-sm-9">
                                <input type="text" id="facebookInput" name="socials[facebook]" class="form-control" placeholder="Enter your Facebook profile" value="{{ $profileDetails->socials['facebook'] ?? '' }}">
                            </div>
                        </div>
                        <div class="card-body row align-items-center card-body-forms">
                            <label for="twitterInput" class="col-sm-3 col-form-label">Twitter:</label>
                            <div class="col-sm-9">
                                <input type="text" id="twitterInput" name="socials[twitter]" class="form-control" placeholder="Enter your Twitter profile" value="{{ $profileDetails->socials['twitter'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>  

            <div class="row">
                <div class="col-md-12 d-flex justify-content-end align-items-center">
                    <input type="submit" class="btn btn-primary" id="submit_form" value="Save Profile" />
                </div>
            </div>
            </form>
        </div>
    </main>
    {{-- footer --}}
    @include('includes.footer')
</div>

{{-- Include Cropper.js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">

<script>
    document.getElementById('profile_picture').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const img = document.getElementById('cropper-image');
                img.src = event.target.result;
                img.style.display = 'block';
                document.getElementById('cropper-container').style.display = 'block';

                const cropper = new Cropper(img, {
                    aspectRatio: 1,
                    viewMode: 2,
                    preview: '.img-preview',
                });

                document.getElementById('crop-image-button').addEventListener('click', function() {
                    const canvas = cropper.getCroppedCanvas({
                        width: 128,
                        height: 128,
                    });
                    canvas.toBlob(function(blob) {
                        const url = URL.createObjectURL(blob);
                        const imgElement = document.getElementById('profile-image');
                        imgElement.src = url;
                        imgElement.style.display = 'block';

                        const croppedImageInput = document.getElementById('cropped_image');
                        croppedImageInput.value = url; // Use URL for simplicity; adjust if necessary

                        cropper.destroy();
                        document.getElementById('cropper-container').style.display = 'none';

                        // Optionally submit the form here if you don't need a separate submit button
                        document.getElementById('profile_form').submit();
                    });
                });
            };
            reader.readAsDataURL(file);
        }
    });
</script>
