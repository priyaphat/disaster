<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Services\ImageService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }
    
    public function run(): void
    {
        $data1 = Product::create([
            'name' => 'น้ำสิงห์ 600 ml',
            'unit_id' => '2',
            'category_id' => '2',
            'quantity' => '100',
        ]);
        $this->imageService->uploadImage($data1->image, Product::IMAGE_FOLDER, Product::class, $data1->id);

        $data2 = Product::create([
            'name' => 'น้ำสิงห์ 600 ml',
            'unit_id' => '1',
            'category_id' => '2',
            'quantity' => '20',
        ]);
        $this->imageService->uploadImage($data2->image, Product::IMAGE_FOLDER, Product::class, $data2->id);
    }
}
