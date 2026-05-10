<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Campaign>
 */
class CampaignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'short_description' => $this->faker->text(100),
            'description' => $this->faker->paragraph,
            'goal_amount' => 10000,
            'current_amount' => 0,
            'deadline' => now()->addMonth(),
            'status' => 'active',
        ];
    }
}
