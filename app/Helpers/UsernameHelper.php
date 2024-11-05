<?php

// app/Helpers/UsernameHelper.php
namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Str;

class UsernameHelper
{
    public static function generateUniqueUsername($firstName, $surName)
    {
        $username = Str::slug($firstName . '-' . $surName);
        $existingUser = User::where('username', $username)->first();

        if ($existingUser) {
            $counter = 1;
            while (User::where('username', $username . '-' . $counter)->exists()) {
                $counter++;
            }
            $username .= '-' . $counter;
        }

        return $username;
    }
}
