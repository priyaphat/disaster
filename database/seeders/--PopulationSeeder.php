<?php

namespace Database\Seeders;

use App\Models\Population;
use App\Services\ImageService;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PopulationSeeder extends Seeder
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = Population::create([
            'prefix_id' => rand(1, 8),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'birth_date' => fake()->date(),
            'gender' => rand(0, 2),
            'phone' => '0912345678',
            'p_address' => 'Chiang ramamamama',
            'p_geography_id' => 4826,
            'c_address' => 'Chiang rai',
            'c_geography_id' => 4826,
            'same_address' => 1,
            'status' => rand(0, 2),
            'shelter_id' => rand(0, 1) ? 1 : null,
            'tracking' => Population::not_tracking,
            'created_date' => Carbon::now()->format('Y-m-d'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        $image = $this->imageService->uploadImage($data->image, Population::IMAGE_FOLDER, Population::class, $data->id);
    }
}
