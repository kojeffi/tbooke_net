<nav class="navbar navbar-expand bg-body-tertiary tbooke-topbar">
		<a class="sidebar-toggle js-sidebar-toggle">
          <i class="hamburger align-self-center"></i>
        </a>
				<div class="navbar-collapse collapse">
					<ul class="navbar-nav navbar-align">
					{{-- New Connection --}}
						 <li class="nav-item">
								<a class="nav-icon" href="{{ route('notifications.index') }}" id="generalalertNotifications">
									<div class="position-relative">
										<i class="align-middle" data-feather="bell"></i>
											@foreach($notifications->where('type', 'New Connection') as $notification)
												<span class="indicator">{{ $connectionNotificationCount }}</span>		
											@endforeach
									</div>
								</a>
						</li>
						{{-- Messages (combined count for user and admin messages) --}}
						<li class="nav-item">
							<a class="nav-icon" href="{{ route('messages.index') }}" id="messagesDropdown">
								<div class="position-relative">
									<i class="align-middle" data-feather="message-square"></i>
									@if($totalMessageNotificationCount > 0)
										<span class="message-indicator">{{ $totalMessageNotificationCount }}</span>
									@endif
								</div>
							</a>
						
						</li>

						
						<li class="nav-item dropdown">
							<a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
                				<i class="align-middle" data-feather="settings"></i>
              				</a>

							<a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
                				@if (Auth::user()->profile_picture)
									<img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile Picture" alt="Profile Picture" class="avatar img-fluid rounded me-1">
								@else
									<img src="{{ asset('/default-images/avatar.png') }}" alt="Default Profile Picture" alt="Profile Picture" class="avatar img-fluid rounded me-1">
								@endif
								<span class="text-dark">
								@if (Auth::user()->profile_type == 'institution')
								{{ Auth::user()->institutionDetails->institution_name }}
								@else
								{{ Auth::user()->first_name }} {{ Auth::user()->surname }}	
								@endif
								</span>
              				</a>
							<div class="dropdown-menu dropdown-menu-end">
								<a class="dropdown-item" href="{{route('profile.showOwn')}}"><i class="align-middle me-1" data-feather="user"></i> Profile</a>
								<a class="dropdown-item" href="{{ route('learning-resources') }}"><i class="align-middle me-1" data-feather="pie-chart"></i> Resources</a>
								<a class="dropdown-item" href="{{route('settings')}}"><i class="align-middle me-1" data-feather="settings"></i> Settings & Privacy</a>
								<a class="dropdown-item" href="{{ route('help-center') }}"><i class="align-middle me-1" data-feather="help-circle"></i> Help Center</a>
								<div class="dropdown-divider"></div>
								<form method="POST" action="{{ route('logout') }}">
                    				@csrf

                    				<x-responsive-nav-link :href="route('logout')"
                            		onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        			{{ __('Log Out') }}
                    				</x-responsive-nav-link>
                				</form>
							</div>
						</li>
					</ul>
				</div>
			</nav>