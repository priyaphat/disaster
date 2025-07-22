<?php

namespace Database\Seeders;

use App\Models\Prefix;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrefixNameSeeder extends Seeder
{
    public function run(): void
    {
        $prefixes = [
            ['name' => 'เด็กชาย', 'type' => 'male'],
            ['name' => 'เด็กหญิง', 'type' => 'female'],
            ['name' => 'นาย', 'type' => 'male'],
            ['name' => 'นาง', 'type' => 'female'],
            ['name' => 'นางสาว', 'type' => 'female'],
            ['name' => 'ด.ช.', 'type' => 'male'],
            ['name' => 'ด.ญ.', 'type' => 'female'],
            ['name' => 'ด.ต.', 'type' => 'male'], // ดาบตำรวจ
            ['name' => 'ส.ต.', 'type' => 'male'], // สิบตรี
            ['name' => 'ส.ต.ท.', 'type' => 'male'], // สิบตำรวจโท
            ['name' => 'ร.ต.', 'type' => 'male'],
            ['name' => 'ร.ต.ต.', 'type' => 'male'],
            ['name' => 'ร.ท.', 'type' => 'male'],
            ['name' => 'ร.อ.', 'type' => 'male'],
            ['name' => 'พ.ต.', 'type' => 'male'],
            ['name' => 'พ.ต.ต.', 'type' => 'male'],
            ['name' => 'พ.ต.ท.', 'type' => 'male'],
            ['name' => 'พ.อ.', 'type' => 'male'],
            ['name' => 'พล.ต.', 'type' => 'male'],
            ['name' => 'ว่าที่ ร.ต.', 'type' => 'male'],
            ['name' => 'ดร.', 'type' => 'any'],
            ['name' => 'นพ.', 'type' => 'male'], // นายแพทย์
            ['name' => 'พญ.', 'type' => 'female'], // แพทย์หญิง
            ['name' => 'ศ.ดร.', 'type' => 'any'], // ศาสตราจารย์ ดร.
            ['name' => 'ศ.', 'type' => 'any'],
            ['name' => 'ผศ.', 'type' => 'any'],
            ['name' => 'รศ.', 'type' => 'any'],
            ['name' => 'พระ', 'type' => 'monk'],
            ['name' => 'พระครู', 'type' => 'monk'],
            ['name' => 'พระมหา', 'type' => 'monk'],
            ['name' => 'สามเณร', 'type' => 'monk'],
            ['name' => 'หม่อมราชวงศ์', 'type' => 'any'],
            ['name' => 'หม่อมหลวง', 'type' => 'any'],
            ['name' => 'อื่น ๆ', 'type' => 'other'],
        ];

        foreach ($prefixes as $prefix) {
            Prefix::create([
                'name' => $prefix['name'],
                'created_by' => 1,
                'updated_by' => 1,
            ]);
        }
    }
}
