<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->count(2)->create()->each(function ($user) {
            $user->approval()->create([
                'is_approved' => 1,
                'approved_by' => $user->id,
                'approved_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $user->image()->create([
                'original_name' => 'default.png',
                'image_name' => 'default.png',
                'image_url' => 'image/users/default.png',
                'image_type' => Image::PRIVATE,
                'parentable_type' => User::class,
                'parentable_id' => $user->id,
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
