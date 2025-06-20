<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datas = [
            'อาหาร',
            'เครื่องดื่ม'
        ];

        foreach ($datas as $data) {
            Category::create(['name' => $data]);
        }
    }
}
