<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $datas = [
            'แพทย์',
            'แพทย์สนาม',
            'แพทย์ฉุกเฉิน',
            'ผู้ช่วยแพทย์',
            'ผู้เชี่ยวชาญด้านการแพทย์ฉุกเฉิน',
            'พยาบาลฉุกเฉิน',
            'นักวิจัยทางการแพทย์',
            'เจ้าหน้าที่กู้ภัย',
            'นักกู้ภัย',
            'ผู้ประสานงานกู้ภัย'
        ];

        foreach ($datas as $data) {
            Position::create(['name' => $data]);
        }
    }
}
