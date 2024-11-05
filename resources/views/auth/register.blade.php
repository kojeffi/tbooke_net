<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="css/style.css" rel="stylesheet" type="text/css" >
    <title>Sign Up | Tbooke</title>
    <link href="static/css/app.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        label.form-label {
            color: #3b7ddd;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

</head>

<body>
    <main class="d-flex w-100">
        <div class="container d-flex flex-column">
            <div class="row vh-100">
                <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto d-table h-100">
                    <div class="d-table-cell align-middle">

                        <div class="text-center mt-4">
                            <h1 class="h2">Get started with Tbooke</h1>
                            <p class="lead">
                                Join Tbooke for professional networking and constructive education-focused conversations.<br>
                                Already have account? <a href="{{route('login')}}">Log In</a>
                            </p>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="m-sm-3">
                                    <form method="POST" action="{{ route('register') }}">
                                        @csrf
                                        @method('post')
                                        <div class="mb-3 profile-type">
                                            <label class="form-label">Select User Type</label>
                                            @error('profile_type')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                            <select name="profile_type" class="form-select mb-3 @error('profile_type') is-invalid @enderror">
                                                <option value="student" {{ old('profile_type') == 'student' ? 'selected' : '' }}>Student/Learner</option>
                                                <option value="teacher" {{ old('profile_type') == 'teacher' ? 'selected' : '' }}>Teacher/Tutor</option>
                                                <option value="institution" {{ old('profile_type') == 'institution' ? 'selected' : '' }}>Institution/Company/School</option>
                                                <option value="other" {{ old('profile_type') == 'other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                        </div>
                                        <div class="mb-3" id="first-name">
                                            <label class="form-label">First Name</label>
                                            @error('first_name')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                            <input class="form-control form-control-lg @error('first_name') is-invalid @enderror" type="text" name="first_name" placeholder="Enter your first name" value="{{ old('first_name') }}" />
                                        </div>
                                        <div class="mb-3" id="surname">
                                            <label class="form-label">Surname</label>
                                            @error('surname')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                            <input class="form-control form-control-lg @error('surname') is-invalid @enderror" type="text" name="surname" placeholder="Enter your surname" value="{{ old('surname') }}" />
                                        </div>
                                        <div class="mb-3" style="display: none" id="institution_name">
                                            <label class="form-label">Name</label>
                                            @error('institution_name')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                            <input class="form-control form-control-lg @error('institution_name') is-invalid @enderror" type="text" name="institution_name" placeholder="Enter institution Name" value="{{ old('institution_name') }}" />
                                        </div>
                                        <div class="mb-3" style="display: none" id="institution_location">
                                            <label class="form-label">Location</label>
                                            @error('institution_location')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                            <input class="form-control form-control-lg @error('institution_location') is-invalid @enderror" type="text" name="institution_location" placeholder="Enter Institution Location" value="{{ old('institution_location') }}" />
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            @error('email')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                            <input class="form-control form-control-lg @error('email') is-invalid @enderror" type="email" name="email" placeholder="Enter your email" value="{{ old('email') }}" />
                                        </div>
                                        
                                            <div class="mb-3">
                                            <label class="form-label">Password</label>
                                            <div class="input-group">
                                                <input class="form-control form-control-lg" type="password" name="password" id="password" placeholder="Enter password" />
                                                <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                                                    <i class="bi bi-eye-slash" id="togglePasswordIcon"></i>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Confirm Password</label>
                                            @error('password')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                            <div class="input-group">
                                                <input class="form-control form-control-lg" type="password" name="password_confirmation" id="passwordConfirmation" placeholder="Enter password" />
                                                <span class="input-group-text" id="togglePasswordConfirmation" style="cursor: pointer;">
                                                    <i class="bi bi-eye-slash" id="togglePasswordConfirmationIcon"></i>
                                                </span>
                                            </div>
                                        </div>
                                            <!-- Privacy Policy Acceptance Checkbox -->
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" name="accept_privacy_policy" class="form-check-input @error('accept_privacy_policy') is-invalid @enderror" id="acceptPrivacyPolicy" {{ old('accept_privacy_policy') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="acceptPrivacyPolicy">
                                                I accept the <a href="/privacy-policy" target="_blank">Privacy Policy</a>
                                            </label>
                                            @error('accept_privacy_policy')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="d-grid gap-2 mt-3">
                                            <input type="submit" class="btn btn-lg btn-primary" value="Sign up"  />
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var selectprofileType = document.querySelector('.profile-type select');
            var institutionName = document.getElementById('institution_name');
            var firstName = document.getElementById('first-name');
            var surname = document.getElementById('surname');
            var institutionLocation = document.getElementById('institution_location');

            // Show/hide fields based on the initial value
            if (selectprofileType.value === 'institution') {
                institutionName.style.display = 'block';
                institutionLocation.style.display = 'block';
                firstName.style.display = 'none';
                surname.style.display = 'none';
            } else {
                institutionName.style.display = 'none';
                institutionLocation.style.display = 'none';
                firstName.style.display = 'block';
                surname.style.display = 'block';
            }

            selectprofileType.addEventListener('change', function() {
                if (selectprofileType.value === 'institution') {
                    institutionName.style.display = 'block';
                    institutionLocation.style.display = 'block';
                    firstName.style.display = 'none';
                    surname.style.display = 'none';
                } else {
                    institutionName.style.display = 'none';
                    institutionLocation.style.display = 'none';
                    firstName.style.display = 'block';
                    surname.style.display = 'block';
                }
            });
        });

    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const togglePasswordIcon = document.querySelector('#togglePasswordIcon');

    const togglePasswordConfirmation = document.querySelector('#togglePasswordConfirmation');
    const passwordConfirmation = document.querySelector('#passwordConfirmation');
    const togglePasswordConfirmationIcon = document.querySelector('#togglePasswordConfirmationIcon');

    togglePassword.addEventListener('click', function () {
        // Toggle the password visibility
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        
        // Toggle icon class
        togglePasswordIcon.classList.toggle('bi-eye');
        togglePasswordIcon.classList.toggle('bi-eye-slash');
    });

    togglePasswordConfirmation.addEventListener('click', function () {
        // Toggle the confirmation password visibility
        const type = passwordConfirmation.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordConfirmation.setAttribute('type', type);
        
        // Toggle icon class
        togglePasswordConfirmationIcon.classList.toggle('bi-eye');
        togglePasswordConfirmationIcon.classList.toggle('bi-eye-slash');
    });
    </script>
    <script src="static/js/app.js"></script>

</body>

</html>