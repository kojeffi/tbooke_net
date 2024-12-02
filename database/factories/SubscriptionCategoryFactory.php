<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubscriptionCategory>
 */
class SubscriptionCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // Predefined expiry durations in hours with corresponding descriptions

        $expiryOptions = [
            1 => '1 hour subscription',
            3 => '3 hours subscription',
            6 => '6 hours subscription',
            12 => '12 hours subscription',
            24 => '1 day subscription',
        ];

        // Randomly select an expiry duration (in hours)
        $expiryHours = $this->faker->randomElement(array_keys($expiryOptions));

        return [
            'name' => $this->faker->word,
            'description' => $expiryOptions[$expiryHours], // Match description to expiry in hours
            'expiry' => $expiryHours * 3600, // Convert hours to seconds
        ];
    }

}
