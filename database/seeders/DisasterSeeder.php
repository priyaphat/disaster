<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Disaster;

class DisasterSeeder extends Seeder
{
    /**
     * php artisan db:seed --class=DisasterSeeder
     */

    public function run(): void
    {
        $disasters = [
            'น้ำท่วม',
            'ไฟไหม้',
            'พายุ',
            'แผ่นดินไหว',
            'ดินถล่ม',
            'โคลนถล่ม',
            'ภูเขาไฟระเบิด',
            'สึนามิ',
            'วาตภัย',
            'ภัยแล้ง',
            'โรคระบาด',
            'รถชน',
            'ไฟฟ้าช็อต',
            'คนตกน้ำ',
            'คนหาย',
            'อุบัติเหตุจากสารเคมี',
            'เหตุจลาจล',
            'สัตว์กัดต่อย',
            'งูเข้าบ้าน',
            'ผึ้งต่อย',
            'สุนัขกัด',
            'คนหมดสติ',
            'หัวใจวาย',
            'ตึกถล่ม',
            'เหตุเพลิงไหม้ในโรงงาน',
            'เด็กติดในรถ',
            'คนติดในลิฟต์',
            'อุบัติเหตุรถไฟ',
            'เรืออับปาง',
        ];

        foreach ($disasters as $disaster) {
            Disaster::create([
                'name' => $disaster,
                'created_by' => 1, 
            ]);
        }
    }
}
