<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\News;
use App\Models\Shelter;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class ShelterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
            
        });

        News::factory()->count(10)->create();
    }
}
