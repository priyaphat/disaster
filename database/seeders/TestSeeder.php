<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        
        $path = database_path('seeders/sql/geography.sql');

        // read the file content
        $sql = file_get_contents($path);

        // run it as raw SQL
        DB::unprepared($sql);
        
        $this->call([
            DepartmentSeeder::class,
            PositionSeeder::class,
            PrefixSeeder::class,
            ShelterPositionSeeder::class,
            DisasterSeeder::class,
            ConfigSeeder::class,
            UserSeeder::class,
            ShelterSeeder::class,
        ]);
    }
}
