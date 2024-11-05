{{-- resources/views/errors/419.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expired</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f8f9fa;
            padding: 50px;
        }
        h1 {
            font-size: 48px;
            color: #dc3545;
        }
        p {
            font-size: 18px;
            color: #6c757d;
        }
        .button {
            margin-top: 30px;
            padding: 15px 30px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            font-size: 18px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1><i class="fas fa-exclamation-triangle"></i>Session Expired</h1>
    <p>Oops, Your session has expired due to inactivity. Please log in again or go to home</p>
    <a href="{{ url('/') }}" class="button">Go Home</a>
</body>
</html>
