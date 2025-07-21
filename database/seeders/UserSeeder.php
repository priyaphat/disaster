<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->count(20)->create()->each(function ($user) {
            $user->approval()->create([
                'is_approved' => 1,
                'approved_by' => $user->id,
                'approved_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        // User::factory()->count(3)->create()->each(function ($user) {
        //     $user->approval()->create([
        //         'is_approved' => 0,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ]);
        // });
    }
}
