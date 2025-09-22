<?php

namespace Database\Factories;

use App\Models\Geography;
use App\Models\Shelter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shelter>
 */
class ShelterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = fake('th_TH');

        $geo = Geography::where('province_name_th', 'เชียงราย')
                ->inRandomOrder()
                ->first();

        return [
            'name' => $faker->city(),
            'phone' => fake()->numerify('0#########'),
            'created_date' => Carbon::now()->format('Y-m-d'),
            'status' => fake()->randomElement([Shelter::OPEN, Shelter::CLOSED]),
            'utilities' => null,
            'geography_id' => $geo->id,
            'province_code'=> $geo->province_code,
            'district_code'=> $geo->district_code,
            'subdistrict_code'=> $geo->subdistrict_code,
            'address' => $faker->city(),
            'max_capacity' => fake()->numberBetween(1, 9999)
        ];
    }
}
