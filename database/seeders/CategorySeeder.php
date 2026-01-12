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
            'อาหาร น้ำดื่ม ของใช้ทั่วไป',
            'ของใช้ทั่วไป',
            'เครื่องจักรกล เครื่องมือเครื่องใช้',
            'อุปกรณ์การแพทย์',
            'ยารักษาโรค'
        ];

        foreach ($datas as $data) {
            Category::create(['name' => $data]);
        }
    }
}
