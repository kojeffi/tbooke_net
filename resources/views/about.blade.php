@include('includes.header')
	
	{{-- Topbar --}}
	<div class="main" style="background-color: #088484;color: #fff;">
			{{-- Main Content --}}
			<main class="content" style="padding-top: 1rem !important;">
               <div class="container-fluid p-0">
                        <div class="mb-3">
                            <a href="/">
                                <img src="/images/tbooke-logo.png" class="logo" alt="">
                            </a>
                            <div style="float: right" class="buttons">
                                <a href="/login" class="btn about-btn">Login</a>
                                <a href="/register" class="btn about-btn">Create Account</a>
                            </div>
                        </div>
                        <div class="row about-rows">
						    <div class="col-12 col-lg-7 about">
                                <h1 class="h3 d-inline align-middle about-h1">About Tbooke</h1>
                                <p>Tbooke media introduces a new era of professional engagement, networking and learning tailored for the education sector.</p>
                                <p>It stands as the premier platform for educational enthusiasts, bridging the gap often found in social media platforms with meaningful conversations that are frequently lost amidst the noise</p>
                                <p>Tbook is where education intersects with social networking, challenging and moving beyond the negative perceptions typically associated with mainstream social media platforms. It promises to be a fertile ground for high quality content designed to cater to the needs of educators and learners of all ages.</p>
                            </div>
                            <div class="col-12 col-lg-5">
                                <div class="card about-card">
								    <img class="card-img-top about-img" src="images/about.jpg" alt="about-tbooke">
							    </div>
                            </div>   
                        </div>
                            <div class="row about-rows">
						        <div class="col-12 col-lg-6">
                                    <img src="images/cbc.png" alt="tbooke" class="img-fluid rounded-circle rounded-circle-about mb-2" width="128" height="128">
                                    <img src="images/primary.png" alt="tbooke" class="img-fluid rounded-circle rounded-circle-about mb-2" width="128" height="128">
                                    <img src="images/High_school.png" alt="tbooke" class="img-fluid rounded-circle rounded-circle-about mb-2" width="128" height="128">
                                    <div>
                                    <img src="images/KCSE_Revision.png" alt="tbooke" class="img-fluid rounded-circle rounded-circle-about mb-2" width="128" height="128">
                                    <img src="images/IGCSE.png" alt="tbooke" class="img-fluid rounded-circle rounded-circle-about mb-2" width="128" height="128">
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="about-card">
                                        <ul class="about-ul">
                                            <li>
                                                A dedicated learning segment that allows for content from professional teachers and students
                                            </li>
                                            <li>
                                                Learning materials and media available for users sorted by grade and age
                                            </li>
                                            <li>
                                                Subscribe to access more premium learning content
                                            </li>
                                            <li>
                                                LIVE learning on school content
                                            </li>
                                        </ul>
							        </div>
                                </div>   
                         </div> 

                        <div class="row about-rows">
						    <div class="col-12 col-lg-7 about">
                                <h1 class="h3 d-inline align-middle about-h1">Learning Resources</h1>
                                <p>Tbooke creates a one stop shop for all school learning resources for all levels of learning</p>
                                <p>Approved educational resources for both in and out of class requirements from publishers, KICD, etc,</p>
                            </div>
                            <div class="col-12 col-lg-5">
                                <div class="about-card">
								    <img class="card-img-top about-img" src="images/learning-resources.png" alt="about-tbooke">
							    </div>
                            </div>   
                        </div>

                        <div class="row about-rows">
                            <div class="col-12 col-lg-6">
                                <div class="about-card">
								    <img class="card-img-top about-img" src="images/content.jpg" alt="about-tbooke">
							    </div>
                            </div> 
						    <div class="col-12 col-lg-6 about content-about">
                                <h1 class="h3 d-inline align-middle about-h1">Content Creation</h1>
                                <p>Tbooke.net offers space for educational content creation both in and outside classroom.</p>
                                <p>The platform is a safe space for all ages. Shift through age and Grade relevant super content and LEARN.</p>
                                <p>With Tbooke, homegrown talent for all age groups is shared for edutainment purposes.</p>
                            </div>  
                        </div>

                        <div class="row about-rows">
						    <div class="col-12 col-lg-7 about">
                                <h1 class="h3 d-inline align-middle about-h1">Tbooke Blueboard</h1>
                                <p>A dedicated space for important educational communications, notices and announcements tailored for local and regional needs</p>
                                <p>It serves as a moderated official channel for delivering verified information directly to the education community while ensuring relevance and authenticity</p>
                            </div>
                            <div class="col-12 col-lg-5">
                                <div class="card about-card">
								    <img class="card-img-top about-img" src="images/blueboard.jpg" alt="about-tbooke">
							    </div>
                            </div>   
                        </div>

                        <div class="row about-rows">
                            <div class="col-12 col-lg-5">
                                <div class="card about-card">
								    <img class="card-img-top about-img" src="images/schoolscorner.jpg" alt="about-tbooke">
							    </div>
                            </div> 
                             <div class="col-12 col-lg-7 about">
                                <h1 class="h3 d-inline align-middle about-h1">School's Corner</h1>
                                <p>Tbooke offers a unique platform for educational institutions to run dedicated social media pages for content creation and publicity</p>
                                <p>The page allows schools to showcase their unique and celebrated potential, achievements, talents and facilities</p>
                            </div>  
                        </div>
               </div>
			</main>
    {{-- footer --}}
	  @include('includes.footer')
	</div>