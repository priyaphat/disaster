<?php

namespace Database\Factories;

use App\Enums\UserType;
use App\Models\Department;
use App\Models\Geography;
use App\Models\Position;
use App\Models\Prefix;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'username' => fake()->userName(),
            'password' => static::$password ??= Hash::make('password'),
            'prefix_id' => Prefix::inRandomOrder()->first()?->id,
            'firstname' => fake()->firstName(),
            'lastname' => fake()->lastName(),
            'phone' => fake()->lastName(),
            'type' => fake()->randomElement(UserType::cases())->value,
            'district_id' => Geography::inRandomOrder()->first()?->district_code,
            'department_id' => Department::inRandomOrder()->first()?->id,
            'position_id' => Position::inRandomOrder()->first()?->id,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
