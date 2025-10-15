<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\Role;
use App\Models\Shelter;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::factory()->count(5)->create([
            'password' => Hash::make('Ab@12345'),
        ])->each(function ($user) {
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

            $user->assignRole(Role::ADMIN);
        });

        // Shelter And Staff
        $user = User::first();
        Auth::login($user);
        Shelter::factory()->count(1)->create()->each(function ($shelter) {
            $shelter->image()->create([
                'original_name' => 'default.png',
                'image_name' => 'default.png',
                'image_url' => 'image/shelter/default.png',
                'image_type' => Image::PUBLIC,
                'parentable_type' => Shelter::class,
                'parentable_id' => $shelter->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

             User::factory()->count(1)->create([
            'password' => Hash::make('Ab@12345'),
        ])->each(function ($user) {
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

            $user->staff()->create([
                'original_name' => 'default.png',
                'user_id' => 'default.png',
                'image_url' => 'image/users/default.png',
                'image_type' => Image::PRIVATE,
                'parentable_type' => User::class,
                'parentable_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        });
    }
}
