@include('includes.header')

{{-- Topbar --}}
<div class="main">
    {{-- Main Content --}}
    <main class="content" style="padding-top: 1rem !important;">
        <div class="container-fluid p-0">
            <div class="mb-3">
                <a href="/">
                    <img src="/images/tbooke-logo.png" class="logo" alt="Tbooke logo">
                </a>
                <div style="float: right" class="buttons">
                    <a href="/login" class="btn about-btn">Login</a>
                    <a href="/register" class="btn about-btn">Register</a>
                </div>
            </div>

            <div class="row about-rows">
                <div class="col-12 col-lg-12 about">
                    <h1 class="h3 d-inline align-middle about-h1">Privacy Policy for Tbooke</h1>
                    <p>At Tbooke, we are committed to protecting your personal information and ensuring that your privacy is safeguarded. This Privacy Policy outlines how we collect, use, and protect the data you provide when using our platform.</p>

                    <h2 class="h5 mt-4">1. Information We Collect</h2>
                    <p>When signing up for Tbooke, we collect personal details such as:</p>
                    <ul class="about-ul">
                        <li>Name</li>
                        <li>Email address</li>
                        <li>Username</li>
                        <li>Educational background and preferences (optional)</li>
                    </ul>
                    <p>We may also collect non-personal data like device information, browser type, and usage statistics to enhance your experience.</p>

                    <h2 class="h5 mt-4">2. How We Use Your Information</h2>
                    <p>Your personal data is used for the following purposes:</p>
                    <ul class="about-ul">
                        <li>Providing access to the platform and its features.</li>
                        <li>Personalizing your experience with relevant content and recommendations.</li>
                        <li>Communicating updates, new features, and educational resources.</li>
                        <li>Improving the platform through data analysis and feedback.</li>
                    </ul>
                    <p>We do not sell or share your personal information with third parties for marketing purposes.</p>

                    <h2 class="h5 mt-4">3. Data Security</h2>
                    <p>We implement robust security measures, including encryption, to protect your data from unauthorized access or misuse. While we strive to ensure the safety of your data, no online platform can guarantee 100% security.</p>

                    <h2 class="h5 mt-4">4. User Control</h2>
                    <p>You have control over your personal information. You can update, correct, or delete your account details by accessing your profile settings. If you wish to delete your account, please contact our support team.</p>

                    <h2 class="h5 mt-4">5. Third-Party Links</h2>
                    <p>Tbooke may contain links to external websites. Please note that we are not responsible for the privacy policies or content of these third-party sites.</p>

                    <h2 class="h5 mt-4">6. Changes to This Policy</h2>
                    <p>We may update this Privacy Policy periodically. If significant changes are made, we will notify you via email or a prominent notice on our platform.</p>

                    <h2 class="h5 mt-4">7. Contact Us</h2>
                    <p>If you have any questions about this Privacy Policy, feel free to reach us at <a href="mailto:privacy@tbooke">privacy@tbooke</a>.</p>

                    <p class="mt-4">By signing up on Tbooke, you agree to this Privacy Policy.</p>
                </div>
            </div>
        </div>
    </main>

    {{-- Footer --}}
    @include('includes.footer')
</div>
