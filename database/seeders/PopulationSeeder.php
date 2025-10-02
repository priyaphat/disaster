<?php

namespace Database\Seeders;

use App\Models\Population;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class PopulationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $user = User::first(); // เลือก user ที่จะใช้
        Auth::login($user);


        Population::factory()
            ->count(999)
            ->create()
            ->each(function ($population) {
                $population->image()->create([
                    'original_name' => 'default.png',
                    'image_name' => 'default.png',
                    'image_url' => 'image/population/default.png',
                    'image_type' => 'public',
                    'parentable_type' => Population::class,
                    'parentable_id' => $population->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $population->shelterpopulation()->create([
                    'population_id' => $population->id,
                    'shelter_id' => 23,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }
}
