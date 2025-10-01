<?php

namespace Database\Factories;

use App\Enums\GenderId;
use App\Models\Prefix;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Population>
 */
class PopulationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'prefix_id' => Prefix::inRandomOrder()->first()?->id,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'birth_date' => '01-01-2000',
            'gender' => fake()->randomElement([1, 2]),
            'phone' => '0987654321',
            'p_address' => '191',
            'p_geography_id' => 4840,
            'c_address' => '191',
            'c_geography_id' => 4840,
            'same_address' => 1,
            'status' => 1,
            'created_date' => Carbon::now()->format('Y-m-d'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        ];
    }
}
