	 <nav id="sidebar" class="sidebar js-sidebar">
			<div class="sidebar-content js-simplebar">
				<a class="sidebar-brand" href="{{ route('admin.admin-panel') }}">
          <img src="/images/tbooke-logo-admin.jpeg" class="admin-side-logo" alt="" />
        </a>
				<ul class="sidebar-nav">

					<li class="sidebar-item active">
						<a class="sidebar-link" href="{{ route('admin.admin-panel') }}">
                        <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Dashboard</span>
                        </a>
					</li>

					<li class="sidebar-item active">
						<a class="sidebar-link" href="{{ route('admin-panel.users') }}">
                        <i class="align-middle" data-feather="users"></i> <span class="align-middle">Users</span>
                        </a>
					</li>

					<li class="sidebar-item active">
						<a class="sidebar-link" href="{{ route('user.subjects') }}">
                        <i class="align-middle" data-feather="file"></i> <span class="align-middle">Subjects</span>
                        </a>
					</li>
					<li class="sidebar-item active">
						<a class="sidebar-link" href="{{ route('topics.index') }}">
                        <i class="align-middle" data-feather="file-plus"></i> <span class="align-middle">Topics</span>
                        </a>
					</li>
					<li class="sidebar-item {{ request()->routeIs('admin-messages.adminMessages') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{ route('admin-messages.adminMessages') }}">
							<i class="align-middle" data-feather="message-square"></i> 
							<span class="align-middle">Messages</span>

							@if($totalUnreadCount > 0)
								<span class="badge bg-danger ms-2">{{ $totalUnreadCount }}</span>
							@endif
						</a>
					</li>
				</ul>
			</div>
		</nav>