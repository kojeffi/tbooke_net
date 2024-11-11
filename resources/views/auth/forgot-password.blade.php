<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="/css/style.css" rel="stylesheet" type="text/css" >
	<title>Forgot Password</title>
	
	<link href="/static/css/app.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>
	<main class="d-flex w-100">
		<div class="container d-flex flex-column">
			<div class="row vh-100">
				<div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto d-table h-100">
					<div class="d-table-cell align-middle">

						<div class="text-center mt-4">
							<h1 class="h2">Request New Password</h1>
						</div>

						<div class="card">
							<div class="card-body">
								<div class="m-sm-3">
                                   <x-auth-session-status class="mb-4" :status="session('status')" />
									<form method="POST" action="{{ route('password.email') }}">
									   @csrf
				
										<div class="mb-3">
											<label class="form-label">Enter Email</label>
											@error('email')
												<div class="text-danger">{{ $message }}</div>
											@enderror
											<input class="form-control form-control-lg @error('email') is-invalid @enderror" type="email" name="email" placeholder="Enter Email" value="{{ old('email') }}" />
										</div>


										<div class="d-grid gap-2 mt-3">
											<input type="submit" class="btn btn-lg btn-primary" value="Submit"  />
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

	<script src="/static/js/app.js"></script>

</body>

</html>