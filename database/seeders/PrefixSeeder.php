<?php

namespace Database\Seeders;

use App\Models\Prefix;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PrefixSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $datas = [
            'นาย',
            'นาง',
            'นางสาว',
            'เด็กชาย',
            'เด็กหญิง',
            'ดร.',
            'ศ.',
            'อื่น ๆ',
        ];

        foreach ($datas as $data) {
            Prefix::create(['name' => $data]);
        }
    }
}
