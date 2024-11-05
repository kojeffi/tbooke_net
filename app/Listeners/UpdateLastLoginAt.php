<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\User;

class UpdateLastLoginAt
{
    public function handle(Login $event)
    {
        // Fetch the user explicitly from the database
        $user = User::find($event->user->id);

        if ($user) {
            // Update the last login timestamp
            $user->last_login_at = now();
            $user->save();
        }
    }
}

