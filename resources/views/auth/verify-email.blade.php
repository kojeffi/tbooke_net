<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 600px;
            margin: 100px auto;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="mb-4 text-sm text-gray-600">
            Thank you for signing up on <b>Tbooke</b>! Please click on the link sent to your email to verify your account
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 font-medium text-sm text-success">
                A new verification link has been sent to the email address you provided during registration.
            </div>
        @endif

        <div class="mt-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <button type="submit" class="btn btn-primary">{{ __('Resend Verification Email') }}</button>
            </form>
        </div>
    </div>
</body>
</html>