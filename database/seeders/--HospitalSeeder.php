<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HospitalSeeder extends Seeder
{

    /*
     * php artisan db:seed --class=HospitalSeeder
    */
    public function run(): void
    {
        $hospitals = [
            ['name' => 'โรงพยาบาลเชียงรายประชานุเคราะห์'],
            ['name' => 'โรงพยาบาลแม่สาย'],
            ['name' => 'โรงพยาบาลเทิง'],
            ['name' => 'โรงพยาบาลพาน'],
            ['name' => 'โรงพยาบาลเชียงของ'],
            ['name' => 'โรงพยาบาลแม่จัน'],
            ['name' => 'โรงพยาบาลเวียงป่าเป้า'],
            ['name' => 'โรงพยาบาลแม่สรวย'],
            ['name' => 'โรงพยาบาลขุนตาล'],
            ['name' => 'โรงพยาบาลเชียงแสน'],
            ['name' => 'โรงพยาบาลเวียงแก่น'],
            ['name' => 'โรงพยาบาลดอยหลวง'],
            ['name' => 'โรงพยาบาลเวียงชัย'],
            ['name' => 'โรงพยาบาลป่าแดด'],
            ['name' => 'โรงพยาบาลพญาเม็งราย'],
            ['name' => 'โรงพยาบาลแม่ฟ้าหลวง'],
            ['name' => 'โรงพยาบาลแม่ลาว'],
        ];

        foreach ($hospitals as $hospital) {
            DB::table('hospitals')->insert([
                'name' => $hospital['name'],
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
