	 <nav id="sidebar" class="sidebar js-sidebar">
			<div class="sidebar-content js-simplebar">
				<a class="sidebar-brand" href="/feed">
          <img src="/images/tbooke-logo.jpeg" class="logo" alt="" />
        </a>
				<ul class="sidebar-nav">
					<li class="sidebar-header">
						Your Pages
					</li>

					<li class="sidebar-item active">
						<a class="sidebar-link" href="{{route('feed')}}">
              <i class="align-middle" data-feather="home"></i> <span class="align-middle">Home</span>
            </a>
					</li>

					<li class="sidebar-item">
						<a class="sidebar-link" href="{{route('tbooke-learning')}}">
              <i class="align-middle" data-feather="book-open"></i> <span class="align-middle">Start Learning</span>
            </a>
					</li>

					<li class="sidebar-item">
						<a class="sidebar-link" href="{{route('profile.showOwn')}}">
              <i class="align-middle" data-feather="user"></i> <span class="align-middle">My Profile</span>
            </a>
					</li>

					<li class="sidebar-header">
						Tools & Components
					</li>
					<li class="sidebar-item">
						<a class="sidebar-link live-class-link" href="{{route('live-classes.index')}}">
              			<i class="align-middle" data-feather="video"></i> <span class="align-middle">Live Classes</span>
            			</a>
					</li>

					<li class="sidebar-item">
						<a class="sidebar-link" href="{{route('groups.index')}}">
              			<i class="align-middle" data-feather="users"></i> <span class="align-middle">Groups</span>
            			</a>
					</li>
					
					<li class="sidebar-item">
						<a class="sidebar-link" href="{{route('settings')}}">
              			<i class="align-middle" data-feather="settings"></i> <span class="align-middle">Settings</span>
            			</a>
					</li>

					<li class="sidebar-item">
						<a class="sidebar-link" href="{{ route('learning-resources') }}">
						<i class="align-middle" data-feather="pie-chart"></i> <span class="align-middle">Resources</span>
						</a>
					</li>

					<li class="sidebar-item">
						<a class="sidebar-link" href="{{ route('schools-corner') }}">
						<i class="align-middle" data-feather="square"></i> <span class="align-middle">Schools Corner</span>
						</a>
					</li>

				</ul>
			</div>
		</nav>