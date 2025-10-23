<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CancelationSeeder extends Seeder
{

    /*
     * php artisan db:seed --class=CancelationSeeder
    */
    public function run(): void
    {
        DB::table('cancelation_reasons')->insert([
            ['name' => 'อื่น ๆ'],
        ]);
    }
}
