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
            ['อุทกภัย', 'images/Flood.png', 1],
            ['ดินถล่ม', 'images/Landslide.png', 1],
            ['ไฟป่า', 'images/ForestFire.png', 1],
            ['หมอกควัน', 'images/Cray.png', 1],
            ['พายุฤดูร้อน', 'images/Storm.png', 1],
            ['แผ่นดินไหว', 'images/Earthquake.png', 1],
            ['อัคคีภัย', 'images/Fire.png', 1],
            ['โรคระบาด', 'images/Epidemic.png', 1],
            ['อพยพ', '', 2],
            ['เข้าศูนย์พักพิง', '', 2],
            ['ขอทีมช่วยเหลือชีวิต', '', 2],
            ['ต้องการสิ่งสนับสนุนปัจจัยสี่', '', 2],
            ['อื่น ๆ', '', 2],
        ];

        foreach ($disasters as $index => $disaster) {
            $id = $disaster[0] === 'อื่น ๆ' ? 99 : null;

            Disaster::create([
                'id' => $id,
                'name' => $disaster[0],
                'image' => $disaster[1] ?? null,
                'pointer' => $disaster[2],
            ]);
        }
    }
}
