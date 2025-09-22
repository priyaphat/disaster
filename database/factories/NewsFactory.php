<?php

namespace Database\Factories;

use App\Models\Shelter;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\News>
 */
class NewsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence($nbWords = 6, $variableNbWords = true),
            'description' => fake()->text($maxNbChars = 200),
            'line_oa' => fake()->randomElement([0, 1]),
            'shelter_id' => (random_int(0, 1) === 1) ? DB::table('shelters')->inRandomOrder()->value('id') : null,
        ];
    }
}
