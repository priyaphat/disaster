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
        // $disasters = [
        //     'น้ำท่วม',
        //     'ไฟไหม้',
        //     'พายุ',
        //     'แผ่นดินไหว',
        //     'ดินถล่ม',
        //     'โคลนถล่ม',
        //     'ภูเขาไฟระเบิด',
        //     'สึนามิ',
        //     'วาตภัย',
        //     'ภัยแล้ง',
        //     'โรคระบาด',
        //     'รถชน',
        //     'ไฟฟ้าช็อต',
        //     'คนตกน้ำ',
        //     'คนหาย',
        //     'อุบัติเหตุจากสารเคมี',
        //     'เหตุจลาจล',
        //     'สัตว์กัดต่อย',
        //     'งูเข้าบ้าน',
        //     'ผึ้งต่อย',
        //     'สุนัขกัด',
        //     'คนหมดสติ',
        //     'หัวใจวาย',
        //     'ตึกถล่ม',
        //     'เหตุเพลิงไหม้ในโรงงาน',
        //     'เด็กติดในรถ',
        //     'คนติดในลิฟต์',
        //     'อุบัติเหตุรถไฟ',
        //     'เรืออับปาง',
        // ];

        $disasters = [
            ['อุทกภัย', 'images/Flood.png'],
            ['ดินถล่ม', 'images/Landslide.png'],
            ['ไฟป่า', 'images/ForestFire.png'],
            ['หมอกควัน', 'images/Cray.png'],
            ['พายุฤดูร้อน', 'images/Storm.png'],
            ['แผ่นดินไหว', 'images/Earthquake.png'],
            ['อัคคีภัย', 'images/Fire.png'],
            ['โรคระบาด', 'images/Epidemic.png'],
        ];

        foreach ($disasters as $disaster) {
            Disaster::create([
                'name' => $disaster[0],
                'image' => $disaster[1]
            ]);
        }
    }
}
