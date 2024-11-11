<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin Sign In | Tbooke</title>
    <link href="/css/custom.css" rel="stylesheet" type="text/css">
    <link href="/static/css/app.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <main class="d-flex w-100 main-login">
        <div class="container d-flex flex-column">
            <div class="row vh-100">
                <div class="col-sm-8 col-md-6 col-lg-6 col-xl-5 mx-auto d-table">
                    <div class="d-table-cell align-middle">
                        <div class="text-center mt-4">
                            <p class="lead">
                                Admin Login
                            </p>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="m-sm-3">
                                    <form method="POST" action="{{ route('admin.login.submit') }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            @error('email')
                                            <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                            <input class="form-control form-control-lg" type="email" name="email" placeholder="Enter your email" />
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Password</label>
                                            <div class="input-group">
                                                <input class="form-control form-control-lg" id="password" type="password" name="password" placeholder="Enter your password" />
                                                <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                                                    <i class="fa fa-eye"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="d-grid gap-2 mt-3">
                                            <input type="submit" class="btn btn-lg btn-primary" value="Sign In">
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
        const togglePassword = document.querySelector("#togglePassword");
        const password = document.querySelector("#password");

        togglePassword.addEventListener("click", function () {
            // Toggle the type attribute
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);

            // Toggle the icon
            this.querySelector('i').classList.toggle("fa-eye");
            this.querySelector('i').classList.toggle("fa-eye-slash");
        });
    </script>
    <script src="/static/js/app.js"></script>
</body>

</html>