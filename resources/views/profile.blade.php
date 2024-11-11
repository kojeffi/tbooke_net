@include('includes.header')
	{{-- Sidebar --}}
	@include('includes.sidebar')
	@include('sweetalert::alert')
		<div class="main">
			{{-- Topbar --}}
        	@include('includes.topbar')
				{{-- Main Content --}}
				<main class="content">
						<!-- Success Modal -->
						<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true"  data-bs-keyboard="true">
							<div class="modal-dialog modal-sm modal-dialog-centered position-absolute end-0">
								<div class="modal-content bg-white">
									<div class="modal-header border-0">
										<h5 class="modal-title text-success" id="successModalLabel">
											Post created successfully
										</h5>
										<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
									</div>
								</div>
							</div>
						</div>

					<!-- Success Modal after sharing post-->
					<div class="modal fade" id="successModalonRepost" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="false">
						<div class="modal-dialog modal-sm modal-dialog-centered position-absolute end-0">
							<div class="modal-content modal-content-success">
								<div class="modal-header">
									<h5 class="modal-title" id="successModalLabel">
										Repost successful
									</h5>
									<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
								</div>
							</div>
						</div>
					</div>


				<div class="container-fluid p-0">
				
					<div class="mb-3">
						<div class="row mb-3">
							<div class="col-md-6 d-flex justify-content-start align-items-center">
								<h1 class="h3 d-inline align-middle">Profile</h1>
								<a href="{{ route('profile.edit') }}" class="btn btn-primary ms-2">Edit Profile</a>
							</div>

							{{-- Tbooke Creator application --}}

							{{-- <div class="col-md-6 tbooke-creator-btn-application">
								@if ($userIsCreator)
									<button type="button" class="btn btn-tbooke mb-2 mb-md-0 me-md-2 tbooke-creator-btn">Tbooke Creator</button>
								@elseif($user->profile_type !== 'student' && $user->profile_type !== 'institution' && $user->profile_type !== 'other')
									<button type="button" class="btn btn-tbooke mb-2 mb-md-0 me-md-2" data-bs-toggle="modal" data-bs-target="#creatorMode">Become a Tbooke Creator</button>
								@endif
							</div> --}}
						</div>
					</div>

					<div class="row">
						<div class="col-md-5 col-xl-5">
							<div class="card mb-3">
								<div class="card-header">
									<h5 class="card-title mb-0">Profile Details</h5>
								</div>
								<div class="card-body text-center">
										@if ($user->profile_picture)
											<img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile Picture" alt="Profile Picture" class="img-fluid rounded-circle mb-2 profile-picture" width="128" height="128">
										@else
											<img src="{{ asset('/default-images/avatar.png') }}" alt="Default Profile Picture" alt="Profile Picture" class="img-fluid rounded-circle mb-2 profile-picture" width="128" height="128">
										@endif
								
								@if ($user->profile_type == 'institution' && $user->institutionDetails)
								<h5 class="card-title mb-0">{{ $user->institutionDetails->institution_name }}</h5>
								@else
								<h5 class="card-title mb-0">{{ Auth::user()->first_name }} {{ Auth::user()->surname }}</h5>	
								@endif
									<div class="text-muted mb-2 capitalize">{{ Auth::user()->profile_type }}</div>
									<div class="mb-3 d-inline-flex card-title text-center">
										<p class="me-2">Connections: {{ $followersCount }}</p>
									</div>
								</div>
                                	<hr class="my-0">
								<div class="card-body ml-3">
									<h5 class="h6 card-title">About Me</h5>
									@if (Auth::user()->profile_type == 'institution')
										  @if ($user->institutionDetails->institution_about)
											<p>{{ $user->institutionDetails->institution_about }}</p>
											@else
											<p>You haven't added about you.</p>
										@endif	
									@else
									  @if ($profileDetails && $profileDetails->about)
											<p>{{ $profileDetails->about }}</p>
											@else
											<p>You haven't added about you.</p>
										@endif	
									@endif
								</div>
								<hr class="my-0">
								<div class="card-body">
									<h5 class="h6 card-title">My Subjects</h5>
										<div class="subject-links">
											@if ($profileDetails && $profileDetails->user_subjects)
												@foreach (explode(',', $profileDetails->user_subjects) as $subject)
													<a href="#" class="badge bg-primary me-1 my-1">{{ $subject }}</a>
												@endforeach
											@else
												<p>You haven't added any subjects.</p>
											@endif
										</div>
								</div>
                                	<hr class="my-0">
								<div class="card-body">
									<h5 class="h6 card-title">Favorites Topics</h5>
										<div class="favorite-topic-links">
											@if ($profileDetails && $profileDetails->favorite_topics)
												@foreach (explode(',', $profileDetails->favorite_topics) as $topic)
													<a href="#" class="badge bg-primary me-1 my-1">{{ $topic }}</a>
												@endforeach
											@else
												<p>You haven't added any favorite topics.</p>
											@endif
										</div>
								</div>
								<hr class="my-0">
								<div class="card-body">
									<h5 class="h6 card-title">Find me on</h5>
									<ul class="list-unstyled mb-0">
										@if ($profileDetails->socials)
											@foreach ($profileDetails->socials as $platform => $link)
												<li class="mb-1"><a target="_blank" href="{{ $link }}">{{ ucfirst($platform) }}</a></li>
											@endforeach
										@else
											<li class="mb-1">No social media profiles found.</li>
										@endif
									</ul>
								</div>
							</div>
						</div>

						<div class="col-md-7 col-xl-7">
						   
						   <div class="card" id="activityFeed">
								<div class="card-header d-flex justify-content-between align-items-center">
									<h5 class="card-title mb-0">Activities</h5>
								</div>
								<div class="card-body h-100">
									@if ($posts->isEmpty())
        							 <p>You do not have any activities.</p>
									@else
									@foreach ($posts as $post)
									@if ($post->is_repost)
									<div class="d-flex align-items-start post-box" id="post-{{ $post->id }}">
													@if ($post->user->id == $user->id)
													<a href="{{ route('profile.showOwn') }}" class="user-image">
															@if ($post->reposter->profile_picture)
																<img src="{{ asset('storage/' . $post->reposter->profile_picture) }}" alt="Profile Picture" class="rounded-circle img-fluid me-2 profile-picture" width="36" height="36">
															@else
																<img src="{{ asset('/default-images/avatar.png') }}" alt="Default Profile Picture" class="rounded-circle img-fluid me-2 profile-picture" width="36" height="36">
															@endif
													</a>
														@else
														<a href="{{ route('profile.show', $post->reposter->username) }}" class="user-image">
															@if ($post->reposter->profile_picture)
																<img src="{{ asset('storage/' . $post->reposter->profile_picture) }}" alt="Profile Picture" class="rounded-circle img-fluid me-2 profile-picture" width="36" height="36">
															@else
																<img src="{{ asset('/default-images/avatar.png') }}" alt="Default Profile Picture" class="rounded-circle img-fluid me-2 profile-picture" width="36" height="36">
															@endif
													</a>
													@endif
													<div class="flex-grow-1">
														<div id="dropdownMenuButton{{ $post->id }}" data-bs-toggle="dropdown" aria-expanded="false" class="float-end dropdown more-dropdown">
															<i class="feather-sm" data-feather="more-horizontal"></i>
															<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $post->id }}">
																@if ($post->user->id == $user->id)
																	<li><a class="dropdown-item delete-post" href="#" data-id="{{ $post->id }}">Delete Post</a></li>
																@else
																	<li><a class="dropdown-item" href="#">Follow {{ $post->user->first_name }}</a></li>
																@endif
															</ul>
														</div>
													<strong>
														@if ($post->reposter->profile_type == 'institution')
															@if ($post->user->id == $user->id)
																<a href="{{ route('profile.showOwn') }}" class="user-name">{{ $post->reposter->institutionDetails->institution_name }}</a>
																@else
																<a href="{{ route('profile.show', $post->reposter->username) }}" class="user-name">{{ $post->reposter->institutionDetails->institution_name }}</a>
															@endif
														@else
														 @if ($post->user->id == $user->id)
															<a href="{{ route('profile.showOwn') }}" class="user-name">{{ $post->reposter->first_name }} {{ $post->reposter->surname }}</a>
														   @else
															<a href="{{ route('profile.show', $post->reposter->username) }}" class="user-name">{{ $post->reposter->first_name }} {{ $post->reposter->surname }}</a>
														 @endif			
														@endif

													</strong> reposted this
													<small class="text-muted">{{ $post->created_at->diffForHumans() }}</small>
													<hr>
													<div class="d-flex align-items-start mt-1 reposted_post">
														@if ($post->originalUser->id == auth()->user()->id)
														<a href="{{ route('profile.showOwn') }}" class="user-image">
															@if ($post->originalUser->profile_picture)
																<img src="{{ asset('storage/' . $post->originalUser->profile_picture) }}" alt="Profile Picture" class="rounded-circle img-fluid me-2 profile-picture" width="36" height="36">
															@else
																<img src="{{ asset('/default-images/avatar.png') }}" alt="Default Profile Picture" class="rounded-circle img-fluid me-2 profile-picture" width="36" height="36">
															@endif
														</a>
														@else
														<a href="{{ route('profile.show', $post->originalUser->username) }}" class="user-image">
															@if ($post->originalUser->profile_picture)
																<img src="{{ asset('storage/' . $post->originalUser->profile_picture) }}" alt="Profile Picture" class="rounded-circle img-fluid me-2 profile-picture" width="36" height="36">
															@else
																<img src="{{ asset('/default-images/avatar.png') }}" alt="Default Profile Picture" class="rounded-circle img-fluid me-2 profile-picture" width="36" height="36">
															@endif
														</a>
														@endif
														<div class="flex-grow-1 reposted_post_media">
															<strong>
																@if ($post->originalUser->profile_type == 'institution')
																	@if ($post->originalUser->id == auth()->user()->id)
																		<a href="{{ route('profile.showOwn') }}" class="user-name">{{ $post->originalUser->institutionDetails->institution_name }}</a>
																		@else
																		<a href="{{ route('profile.show', $post->originalUser->username) }}" class="user-name">{{ $post->originalUser->institutionDetails->institution_name }}</a>
																	@endif
																@else
																	@if ($post->originalUser->id == auth()->user()->id)
																		<a href="{{ route('profile.showOwn') }}" class="user-name">{{ $post->originalUser->first_name }} {{ $post->originalUser->surname }}</a>
																		@else
																		<a href="{{ route('profile.show', $post->originalUser->username) }}" class="user-name">{{ $post->originalUser->first_name }} {{ $post->originalUser->surname }}</a>
																	@endif
																@endif

															</strong>
															<small class="text-navy">{{ $post->originalPost->created_at->diffForHumans() }}</small>
															@php
																// Check if the original post content contains a URL
																$isUrl = preg_match('/\bhttps?:\/\/\S+/i', $post->originalPost->content);
															@endphp

															@if(!$isUrl) <!-- Display content only if it's NOT a URL -->
															<p>{!! $post->originalPost->content !!}</p>
															@endif

															
															<div class="post-link-preview">
																@if($post->originalPost->link_preview)
																	@php
																		$linkPreview = json_decode($post->originalPost->link_preview);
																		$url = parse_url($post->originalPost->content);
																		$domain = isset($url['host']) ? $url['host'] : 'Unknown Domain';
																	@endphp
																	<div>
																		<h3 class="mt-3">{{ $linkPreview->title }}</h3>
																		<p>{{ Str::limit(strip_tags($linkPreview->description), 150) }}</p>
																		<a href="{{ $post->originalPost->content }}" target="_blank">
																			<img src="{{ $linkPreview->image }}" alt="Link Preview">
																		</a>
																		<a class="preview-a" href="{{ $post->originalPost->content }}" target="_blank">
																			<p class="preview-domain">{{ $domain }}</p>
																		</a>
																	</div>
																@endif
															</div>
															<!-- Display Media Files -->
															@if ($post->media_path)
																@php
																	$mediaCount = count($post->media_path);
																	$maxDisplayCount = 6; // Max number of media to display directly
																@endphp
																<div class="row g-2 mt-1">
																	@foreach ($post->media_path as $index => $media)
																		@php
																			$extension = pathinfo($media, PATHINFO_EXTENSION);
																			// Determine the column size based on the number of media files
																			$columnSize = ($mediaCount == 1) ? 'col-md-12' : (($mediaCount == 2) ? 'col-md-6' : 'col-md-4');
																		@endphp

																		{{-- Show the +X counter on the last image if media exceeds 6 --}}
																		@if ($index == $maxDisplayCount - 1 && $mediaCount > $maxDisplayCount)
																			<div class="col-12 col-md-4 position-relative">
																				<a href="{{ asset('storage/' . $media) }}" data-lightbox="post-gallery-{{ $post->id }}">
																					<img src="{{ asset('storage/' . $media) }}" class="img-fluid mb-2" alt="post-media">
																					<div class="overlay-counter">
																						<span>+{{ $mediaCount - $maxDisplayCount }}</span>
																					</div>
																				</a>
																			</div>
																			@break {{-- Stop loop after showing the +X image --}}
																		@elseif ($index < $maxDisplayCount - 1)
																			{{-- Display regular media items up to the max limit --}}
																			<div class="col-12 {{ $columnSize }}">
																				@if (in_array($extension, ['jpeg', 'jpg', 'png', 'gif', 'PNG']))
																					<a href="{{ asset('storage/' . $media) }}" data-lightbox="post-gallery-{{ $post->id }}">
																						<img src="{{ asset('storage/' . $media) }}" class="img-fluid mb-2" alt="post-media">
																					</a>
																				@elseif (in_array($extension, ['mp4', 'mov', 'avi', 'wmv']))
																					<video controls class="img-fluid mb-2" style="max-width: 100%">
																						<source src="{{ asset('storage/' . $media) }}" type="video/{{ $extension }}">
																						Your browser does not support the video tag.
																					</video>
																				@endif
																			</div>
																		@endif
																	@endforeach
																</div>

																{{-- Hidden images that are beyond the first 6, allowing Lightbox to show them when the last column is clicked --}}
																@if ($mediaCount > $maxDisplayCount)
																	@for ($i = $maxDisplayCount; $i < $mediaCount; $i++)
																		@php
																			$media = $post->media_path[$i];
																			$extension = pathinfo($media, PATHINFO_EXTENSION);
																		@endphp
																		{{-- Hidden media links to be displayed when the +X counter is clicked --}}
																		<a href="{{ asset('storage/' . $media) }}" data-lightbox="post-gallery-{{ $post->id }}" class="d-none"></a>
																	@endfor
																@endif
															@endif
															
															<div class="engagement-metrics">
																@if($post->originalPost->likes->contains('id', auth()->user()->id))
																	<div class="unlike-section">
																		<form id="unlikeForm-repost-{{ $post->originalPost->id }}" 
																			  action="{{ route('post.unlike', $post->originalPost->id) }}" 
																			  method="POST" 
																			  class="like-unlike-form" 
																			  data-post-id="{{ $post->originalPost->id }}" 
																			  data-action-like="{{ route('post.like', $post->originalPost->id) }}" 
																			  data-action-unlike="{{ route('post.unlike', $post->originalPost->id) }}">
																			@csrf
																			<button type="submit" id="unlikeButton-repost-{{ $post->originalPost->id }}" 
																					class="btn btn-sm btn-secondary rounded mt-1 engage-btns unlike-btn" 
																					data-post-id="{{ $post->originalPost->id }}">
																				<span class="thumbs-down"><i class="feather-sm" data-feather="thumbs-down"></i></span>
																				<span class="likes-count" id="likes-count-repost-{{ $post->originalPost->id }}">{{ $post->originalPost->likes->count() }}</span>
																			</button>
																		</form>
																	</div>
																@else
																	<div class="like-section">
																		<form id="likeForm-repost-{{ $post->originalPost->id }}" 
																			  action="{{ route('post.like', $post->originalPost->id) }}" 
																			  method="POST" 
																			  class="like-unlike-form" 
																			  data-post-id="{{ $post->originalPost->id }}" 
																			  data-action-like="{{ route('post.like', $post->originalPost->id) }}" 
																			  data-action-unlike="{{ route('post.unlike', $post->originalPost->id) }}">
																			@csrf
																			<button type="submit" id="likeButton-repost-{{ $post->originalPost->id }}" 
																					class="btn btn-sm btn-secondary rounded mt-1 engage-btns like-btn" 
																					data-post-id="{{ $post->originalPost->id }}">
																				<span class="thumbs-up"><i class="feather-sm" data-feather="thumbs-up"></i></span>
																				<span class="likes-count" id="likes-count-repost-{{ $post->originalPost->id }}">{{ $post->originalPost->likes->count() }}</span>
																			</button>
																		</form>
																	</div>
																@endif

																<div class="comment-section">
																	<a class="btn btn-sm btn-secondary mt-1 rounded comment-toggle-btn engage-btns comment-btn">
																		<span class="d-none d-md-inline"><i class="feather-sm" data-feather="message-square"></i></span>
																		<span class="d-inline d-md-none"><i class="feather-sm" data-feather="message-square"></i></span>
																		<span class="comments-count">{{ $post->originalPost->comments->count() }}</span>
																	</a>
																</div>

																<div class="repost-section">
																	<form id="repost-{{ $post->originalPost->id }}" action="{{ route('posts.repost', $post->originalPost->id) }}" method="POST" class="repost-form-on-repost" data-post-id="{{ $post->originalPost->id }}">
																		@csrf
																		<button type="submit" id="repostButton-{{ $post->originalPost->id }}" class="btn btn-sm btn-secondary rounded mt-1 repost-btn engage-btns like-btn">
																			<span class="d-none d-md-inline"><i class="feather-sm" data-feather="repeat"></i></span>
																			<span class="d-inline d-md-none"><i class="feather-sm" data-feather="repeat"></i></span>
																			<span class="repost-count">{{ $post->originalPost->repost_count }}</span>
																		</button>
																	</form>
																</div>
															</div>
																	<div class="card-body comment-box">
																		<form id="createCommentForm{{ $post->originalPost->id }}">
																			@csrf
																			<input type="hidden" name="post_id" value="{{ $post->originalPost->id }}">
																			<div class="mb-3 mt-3">
																				<textarea class="form-control comment-area" name="content" id="commentContent{{ $post->originalPost->id }}" rows="2" placeholder="Post your comment"></textarea>
																			</div>
																		</form>
																		<button type="button" id="submitCommentBtn{{ $post->originalPost->id }}" class="btn btn-primary submit-comment-btn">Submit</button>
																		@foreach ($post->originalPost->comments as $comment)
																			<div class="comment-item d-flex align-items-start mt-1">
																				<div class="profile_image_in_comment">
																					<a class="#" href="#">
																						@if ($comment->user->profile_picture)
																							<img src="{{ asset('storage/' . $comment->user->profile_picture) }}" alt="{{ $comment->user->first_name }}'s Profile Picture" class="rounded-circle img-fluid me-2 profile-picture" width="36" height="36">
																						@else
																							<img src="{{ asset('/default-images/avatar.png') }}" alt="Default Profile Picture" class="rounded-circle img-fluid me-2 profile-picture" width="36" height="36">
																						@endif
																					</a>
																				</div>
																				<div class="flex-grow-1 comment-item-inner-box">
																					<small class="float-end text-navy">{{ $comment->created_at->diffForHumans() }}</small>
																					<div class="text-muted p-2 mt-1">
																						<div>
																							<strong>
																								@if ($comment->user->profile_type == 'institution' && $comment->user->institutionDetails)
																									{{ $comment->user->institutionDetails->institution_name }}
																								@else
																									{{ $comment->user->first_name }} {{ $comment->user->surname }}
																								@endif
																							</strong>
																						</div>
																						<div>{{ $comment->content }}</div>
																					</div>
																				</div>
																			</div>
																			<hr>
																		@endforeach
																	</div>
														</div>
													</div>
												</div>
											</div>
									@else
									<div class="d-flex align-items-start post-box" id="post-{{ $post->id }}">
											@if ($post->user->id == $user->id)
											<a href="{{ route('profile.showOwn') }}" class="user-image">
												@if ($post->user->profile_picture)
													<img src="{{ asset('storage/' . $post->user->profile_picture) }}" alt="Profile Picture" class="rounded-circle img-fluid me-2 profile-picture" width="36" height="36">
												@else
													<img src="{{ asset('/default-images/avatar.png') }}" alt="Default Profile Picture" class="rounded-circle img-fluid me-2 profile-picture" width="36" height="36">
												@endif
											</a>
											@else
											<a href="{{ route('profile.show', $post->user->username) }}" class="user-image">
												@if ($post->user->profile_picture)
													<img src="{{ asset('storage/' . $post->user->profile_picture) }}" alt="Profile Picture" class="rounded-circle img-fluid me-2 profile-picture" width="36" height="36">
												@else
													<img src="{{ asset('/default-images/avatar.png') }}" alt="Default Profile Picture" class="rounded-circle img-fluid me-2 profile-picture" width="36" height="36">
												@endif
											</a>
											@endif
											<div class="flex-grow-1">
													<div id="dropdownMenuButton{{ $post->id }}" data-bs-toggle="dropdown" aria-expanded="false" class="float-end dropdown more-dropdown">
														<i class="feather-sm" data-feather="more-horizontal"></i>
															<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $post->id }}">
																@if ($post->user->id == $user->id)
																	<li><a class="dropdown-item delete-post" href="#" data-id="{{ $post->id }}">Delete Post</a></li>
																@else
																	<li><a class="dropdown-item" href="#">Follow {{ $post->user->first_name }}</a></li>
																@endif
															</ul>
													</div>
												<strong>
												@if ($post->user->id == $user->id)
													@if ($user->profile_type == 'institution' && $user->institutionDetails)
													<a href="{{ route('profile.showOwn') }}" class="user-name">{{ $user->institutionDetails->institution_name }}</a>
													@else
													<a href="{{ route('profile.showOwn') }}" class="user-name">{{ $post->user->first_name }} {{ $post->user->surname }}</a>	
													@endif
												@else
												<a href="{{ route('profile.show', $post->user->username) }}" class="user-name">{{ $post->user->first_name }} {{ $post->user->surname }}</a>
												@endif
												</strong>
												<small class="text-navy">{{ $post->created_at->diffForHumans() }}</small>
												@php
												// Regular expression to check if the content is a URL
												$isUrl = preg_match('/\bhttps?:\/\/\S+/i', $post->content);
											@endphp

											@if(!$isUrl) <!-- Display content only if it's NOT a URL -->
											<p>{!! $post->content !!}</p>

											@endif
											<div class="post-link-preview">
												@if($post->link_preview)
													@php
														$linkPreview = json_decode($post->link_preview);
														$url = parse_url($post->content);
														$domain = isset($url['host']) ? $url['host'] : 'Unknown Domain';
													@endphp
													<div>
														<h3 class="mt-3">{{ $linkPreview->title }}</h3>
														<p>{{ Str::limit(strip_tags($linkPreview->description), 150) }}</p>
														<a href="{{ $post->content }}" target="_blank"><img src="{{ $linkPreview->image }}" alt="Link Preview"></a>
														<a class="preview-a" href="{{ $post->content }}" target="_blank"><p class="preview-domain">{{ $domain }}</p></a>
													</div>
												@endif
											</div>	
												
														@if ($post->media_path)
																@php
																	$mediaCount = count($post->media_path);
																	$maxDisplayCount = 6; // Max number of media to display directly
																@endphp
																<div class="row g-2 mt-1">
																	@foreach ($post->media_path as $index => $media)
																		@php
																			$extension = pathinfo($media, PATHINFO_EXTENSION);
																			// Determine the column size based on the number of media files
																			$columnSize = ($mediaCount == 1) ? 'col-md-12' : (($mediaCount == 2) ? 'col-md-6' : 'col-md-4');
																		@endphp

																		{{-- Show the +X counter on the last image if media exceeds 6 --}}
																		@if ($index == $maxDisplayCount - 1 && $mediaCount > $maxDisplayCount)
																			<div class="col-12 col-md-4 position-relative">
																				<a href="{{ asset('storage/' . $media) }}" data-lightbox="post-gallery-{{ $post->id }}">
																					<img src="{{ asset('storage/' . $media) }}" class="img-fluid mb-2" alt="post-media">	
																					<div class="overlay-counter">
																						<span>+{{ $mediaCount - $maxDisplayCount }}</span>
																					</div>
																				</a>	
																			</div>
																			@break {{-- Stop loop after showing the +X image --}}
																		@elseif ($index < $maxDisplayCount - 1)
																			{{-- Display regular media items up to the max limit --}}
																			<div class="col-12 {{ $columnSize }}">
																				@if (in_array($extension, ['jpeg', 'jpg', 'png', 'gif', 'PNG']))
																					<a href="{{ asset('storage/' . $media) }}" data-lightbox="post-gallery-{{ $post->id }}">
																						<img src="{{ asset('storage/' . $media) }}" class="img-fluid mb-2" alt="post-media">
																					</a>
																				@elseif (in_array($extension, ['mp4', 'mov', 'avi', 'wmv']))
																					<video controls class="img-fluid mb-2" style="max-width: 100%">
																						<source src="{{ asset('storage/' . $media) }}" type="video/{{ $extension }}">
																						Your browser does not support the video tag.
																					</video>
																				@endif
																			</div>
																		@endif
																	@endforeach
																</div>

																{{-- Hidden images that are beyond the first 6, allowing Lightbox to show them when the last column is clicked --}}
																@if ($mediaCount > $maxDisplayCount)
																	@for ($i = $maxDisplayCount; $i < $mediaCount; $i++)
																		@php
																			$media = $post->media_path[$i];
																			$extension = pathinfo($media, PATHINFO_EXTENSION);
																		@endphp
																		{{-- Hidden media links to be displayed when the +X counter is clicked --}}
																		<a href="{{ asset('storage/' . $media) }}" data-lightbox="post-gallery-{{ $post->id }}" class="d-none"></a>
																	@endfor
																@endif
															@endif

															<div class="engagement-metrics">
																@if($post->likes->contains('id', auth()->user()->id))
																<div class="unlike-section">
																	<form id="unlikeForm-{{ $post->id }}" action="{{ route('post.unlike', $post->id) }}" method="POST" class="like-unlike-form" data-post-id="{{ $post->id }}" data-action-like="{{ route('post.like', $post->id) }}" data-action-unlike="{{ route('post.unlike', $post->id) }}">
																		@csrf
																		<button type="submit" id="unlikeButton-{{ $post->id }}" class="btn btn-sm btn-secondary rounded mt-1 engage-btns unlike-btn engage-unlike-btn">
																			<span class="d-none d-md-inline thumbs-down"><i class="feather-sm" data-feather="thumbs-down"></i></span>
																			<span class="d-inline d-md-none thumbs-down"><i class="feather-sm" data-feather="thumbs-down"></i></span>
																			<span class="likes-count" id="likes-count-{{ $post->id }}">{{ $post->likes->count() }}</span>
																		</button>
																	</form>
																</div>
																@else
																<div class="like-section">
																		<form id="likeForm-{{ $post->id }}" action="{{ route('post.like', $post->id) }}" method="POST" class="like-unlike-form" data-post-id="{{ $post->id }}" data-action-like="{{ route('post.like', $post->id) }}" data-action-unlike="{{ route('post.unlike', $post->id) }}">
																			@csrf
																			<button type="submit" id="likeButton-{{ $post->id }}" class="btn btn-sm btn-secondary rounded mt-1 engage-btns like-btn">
																				<span class="d-none d-md-inline thumbs-up"><i class="feather-sm" data-feather="thumbs-up"></i></span>
																				<span class="d-inline d-md-none thumbs-up"><i class="feather-sm" data-feather="thumbs-up"></i></span>
																				<span class="likes-count" id="likes-count-{{ $post->id }}">{{ $post->likes->count() }}</span>
																			</button>
																		</form>
																</div>
																@endif
																
																<div class="comment-section">
																	<a class="btn btn-sm btn-secondary mt-1 rounded comment-toggle-btn engage-btns comment-btn">
																		<span class="d-none d-md-inline"><i class="feather-sm" data-feather="message-square"></i></span>
																		<span class="d-inline d-md-none"><i class="feather-sm" data-feather="message-square"></i></span>
																		<span class="comments-count">{{ $post->comments->count() }}</span>
																	</a>
																</div>
																
																<div class="repost-section">
																	<form id="repost-{{ $post->id }}" action="{{ route('posts.repost', $post->id) }}" method="POST" class="repost-form" data-post-id="{{ $post->id }}">
																		@csrf
																		<button type="submit" id="repostButton-{{ $post->id }}" class="btn btn-sm btn-secondary rounded mt-1 repost-btn engage-btns">
																			<span class="d-none d-md-inline"><i class="feather-sm" data-feather="repeat"></i></span>
																			<span class="d-inline d-md-none"><i class="feather-sm" data-feather="repeat"></i></span>
																			<span class="repost-count">{{ $post->repost_count }}</span>
																		</button>
																	</form>
																</div>
															</div>			

												<div class="card-body comment-box">
													<form id="createCommentForm{{ $post->id }}">
														@csrf
														<input type="hidden" name="post_id" value="{{ $post->id }}">
														<div class="mb-3 mt-3">
															<textarea class="form-control comment-area" name="content" id="commentContent{{ $post->id }}" rows="2" placeholder="Post your comment"></textarea>
														</div>
													</form>
													<button type="button" id="submitCommentBtn{{ $post->id }}" class="btn btn-primary submit-comment-btn">Submit</button>

													@foreach ($post->comments as $comment)
														<div class="comment-item d-flex align-items-start mt-1">
															<div class="profile_image_in_comment">
																<a class="#" href="#">
																	@if ($comment->user->profile_picture)
																		<img src="{{ asset('storage/' . $comment->user->profile_picture) }}" alt="{{ $comment->user->first_name }}'s Profile Picture" class="rounded-circle img-fluid me-2 profile-picture" width="36" height="36">
																	@else
																		<img src="{{ asset('/default-images/avatar.png') }}" alt="Default Profile Picture" class="rounded-circle img-fluid me-2 profile-picture" width="36" height="36">
																	@endif
																</a>
															</div>
															<div class="flex-grow-1 comment-item-inner-box">
																<small class="float-end text-navy">{{ $comment->created_at->diffForHumans() }}</small>
																<div class="text-muted p-2 mt-1">
																	@if ($comment->user->profile_type == 'institution')
																		<div><strong>{{ $comment->user->institutionDetails->institution_name }}</strong></div>
																		@else
																		<div><strong>{{ $comment->user->first_name }} {{ $comment->user->surname }}</strong></div>
																	@endif
																	<div>{{ $comment->content }}</div>
																</div>
															</div>
														</div>
													@endforeach
												</div>
											</div>
										</div>	
									@endif	
									@endforeach

									@endif
								</div>
							</div>
									<!-- Create Post Modal -->
										<div class="modal fade" id="createPost" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
											<div class="modal-dialog">
												<div class="modal-content">
													<div class="modal-header">
														<h5 class="modal-title" id="createPostLabel">Create Post</h5>
														<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
													</div>
													<div class="modal-body">
														<form id="createPostForm">
															@csrf
															<div class="mb-3">
																<label for="postContent" class="form-label">Post Content</label>
																<textarea class="form-control" id="postContent" name="content" rows="7" placeholder="Enter your post content"></textarea>
															</div>
														</form>
													</div>
													<div class="modal-footer">
														<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
														<button type="button" class="btn btn-primary" id="submitPostBtn">Create</button>
													</div>
												</div>
											</div>
										</div>

										<!-- Become a creator -->
										<div class="modal fade" id="creatorMode" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
											<div class="modal-dialog">
												<div class="modal-content">
													<div class="modal-header">
														<h5 class="modal-title" id="">Become a Creator</h5>
														<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
													</div>
													<div class="modal-body">
														<form id="creatorModeForm">
															@csrf
															<div class="mb-3">
																<select name="creator_subjects[]" multiple class="chosen-select-width form-select" tabindex="16">
																	<option disabled selected>Which subjects do you consider yourself an expert?</option>
																	<option value="Geography">Geography</option>
																	<option value="Mathematics">Mathematics</option>
																	<option value="English">English</option>
																	<option value="Kiswahili">Kiswahili</option>
																	<option value="Business">Business</option>
																</select>
															</div>
															<div class="mb-3">
																<select name="creator_expertise[]" multiple class="chosen-select-width form-select" tabindex="16">
																	<option disabled selected>Which learning grades will be your main target?</option>
																	<option value="pre_school">Pre School</option>
																	<option value="grade_1_6">Grades 1-6</option>
																	<option value="cbc_content">CBC Content</option>
																	<option value="jss">Junior Secondary School</option>
																	<option value="high_school">High School</option>
																</select>
															</div>
															<div class="mb-3">
																<textarea class="form-control"  name="the_why" rows="7" placeholder="Why do you want to become a Tbooke creator"></textarea>
															</div>
														</form>
													</div>
													<div class="modal-footer">
														<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
														<button type="button" class="btn btn-primary" id="submitRequestBtn">Submit</button>
													</div>
												</div>
											</div>
										</div>

										
									</div>
							</div>
					</div>
			</main>
				<script>
					const postStoreRoute = "{{ route('posts.store') }}";
					const commentStoreRoute = "{{ route('comment.store') }}";
					const deletePostRoute = "{{ route('posts.destroy', ':post_id') }}";
				</script>
	  	{{-- footer --}}
	  	@include('includes.footer')
	</div>

