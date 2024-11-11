<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        // If the user has already verified their email, redirect to /
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended('/?verified=1');
        }

        // If the email is newly verified, mark it as verified
        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        // Redirect to / after successful verification
        return redirect()->intended('/?verified=1');
    }
}
