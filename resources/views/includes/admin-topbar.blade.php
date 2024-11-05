

<nav class="navbar navbar-expand bg-body-tertiary">
		<a class="sidebar-toggle js-sidebar-toggle">
          <i class="hamburger align-self-center"></i>
        </a>
				<div class="navbar-collapse collapse">
					<ul class="navbar-nav navbar-align">
						<li class="nav-item dropdown">
							<a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
                				<i class="align-middle" data-feather="settings"></i>
              				</a>
							<a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
								<img src="{{ asset('/default-images/avatar.png') }}" alt="Default Profile Picture" alt="Profile Picture" class="avatar img-fluid rounded me-1">
              				<span class="text-dark">{{ Auth::guard('admin')->user()->name }}</span>
							</a>
							<div class="dropdown-menu dropdown-menu-end">
								<a class="dropdown-item" href="#">Settings</a>
								<div class="dropdown-divider"></div>
								<form method="POST" action="{{ route('admin.logout') }}">
                    				<form method="POST" action="{{ route('admin.logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button> <!-- Logout button -->
                                    </form>
                				</form>
							</div>
						</li>
					</ul>
				</div>
			</nav>