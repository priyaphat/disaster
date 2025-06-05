<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Config;

class ConfigSeeder extends Seeder
{
    /**
     * php artisan db:seed --class=ConfigSeeder
     */

    public function run(): void
    {
        $configs = [
            ['name' => '@AlertNowCRI', 'type' => 2],
            ['name' => 'ประกาศข่าว จากบอร์ดประชาสัมพันธ์', 'type' => 1, 'description' => [
                "user" => 0,
                "population" => 0
            ]],
            ['name' => 'ชื่อฟังก์ชั้น', 'type' => 1, 'description' => [
                "user" => 0,
                "population" => 0
            ]],
            ['name' => 'ชื่อฟังก์ชั้น', 'type' => 1, 'description' => [
                "user" => 0,
                "population" => 0
            ]],
            ['name' => 'ชื่อฟังก์ชั้น', 'type' => 1, 'description' => [
                "user" => 0,
                "population" => 0
            ]],
            ['name' => 'ชื่อฟังก์ชั้น', 'type' => 1, 'description' => [
                "user" => 0,
                "population" => 0
            ]],
            ['name' => 'ชื่อฟังก์ชั้น', 'type' => 1, 'description' => [
                "user" => 0,
                "population" => 0
            ]],
        ];

        foreach ($configs as $config) {
            Config::create([
                'name' => $config['name'],
                'type' => $config['type'],
                'status' => 1,
                'description' => $config['description'] ?? null,
            ]);
        }
    }
}
