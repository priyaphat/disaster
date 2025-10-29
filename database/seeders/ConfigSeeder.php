<?php

namespace Database\Seeders;

use App\Http\Controllers\API\LineController;
use Illuminate\Database\Seeder;
use App\Models\Config;

class ConfigSeeder extends Seeder
{
    /**
     * php artisan db:seed --class=ConfigSeeder
     * type 1 line , 2 line id , 3 weather , 4 chart , 5 disasterCornjob , 6 lineCount
     */

    public function run(): void
    {
        $configs = [
            ['name' => '@AlertNowCRI', 'type' => 2],
            ['name' => 'แจ้งเตือน ยุติเหตุภัยพิบัติ', 'type' => 1, 'description' => [
                "line_type" => [
                    LineController::Disaster_end,
                ],
                "user" => 0,
                "population" => 0
            ]],
            ['name' => 'แจ้งเตือน เปิดทำการศูนย์พักพิง', 'type' => 1, 'description' => [
                "line_type" => [LineController::Shelter_post],
                "user" => 0,
                "population" => 0
            ]],
            ['name' => 'แจ้งเตือน ปิดทำการศูนย์พักพิง', 'type' => 1, 'description' => [
                "line_type" => [LineController::Shelter_end],
                "user" => 0,
                "population" => 0
            ]],
            ['name' => 'แจ้งเตือน อัปเดตสาธารณประโยชน์ที่ได้รับความเสียหาย', 'type' => 1, 'description' => [
                "line_type" => [LineController::Damaged_utility_post, LineController::Damaged_utility_end],
                "user" => 0,
                "population" => 0
            ]],
            ['name' => 'แจ้งเตือน คำร้องขอทรัพยากร', 'type' => 1, 'description' => [
                "line_type" => [LineController::Resource],
                "user" => 0,
                "population" => 0
            ]],
            ['name' => 'แจ้งเตือน คำร้องขอใหม่', 'type' => 1, 'description' => [
                "line_type" => [LineController::Emc_request_post, LineController::Emc_request_unaccept],
                "user" => 0,
            ]],
            ['name' => 'แจ้งเตือน รายการรอช่วยเหลือใหม่ (ส่งให้หัวหน้าทีมที่เกี่ยวข้องอนุมัติ)', 'type' => 1, 'description' => [
                "line_type" => [LineController::Emc_request_post_team],
                "user" => 0,
            ]],
            ['name' => 'แจ้งเตือน รายการรอช่วยเหลือใหม่ (ส่งให้สมาชิกในทีมที่เกี่ยวข้องเมื่ออนุมัติสำเร็จ)', 'type' => 1, 'description' => [
                "line_type" => [LineController::Emc_assigned],
                "user" => 0,
            ]],
            ['name' => 'แจ้งเตือน รายการรอช่วยเหลือถูกยกเลิก (ส่งให้หัวหน้าทีมที่เกี่ยวข้อง)', 'type' => 1, 'description' => [
                "line_type" => [LineController::Emc_request_end],
                "user" => 0,
            ]],
            ['name' => 'แจ้งเตือน รายงานผลการช่วยเหลือ (ส่งให้เจ้าหน้าที่ส่วนกลาง (ทุกสถานะ))', 'type' => 1, 'description' => [
                "line_type" => [LineController::Emc_success, LineController::Emc_unsuccess, LineController::Hospital],
                "user" => 0,
            ]],
            ['name' => 'แจ้งเตือน คำร้องขอลงทะเบียนใหม่ (ส่งให้เจ้าหน้าที่ส่วนกลางอนุมัติ)', 'type' => 1, 'description' => [
                "line_type" => [LineController::User_register],
                "user" => 0,
            ]],
            ['name' => 'แจ้งเตือน นัดหมายตรวจเยี่ยมศูนย์พักพิงใหม่ (ส่งให้ผู้ดูแลภายในศูนย์ที่เกี่ยวข้อง)', 'type' => 1, 'description' => [
                "line_type" => [LineController::Shelter_appointment],
                "user" => 0,
            ]],
            ['name' => 'แจ้งเตือน แก้ไขนัดหมายตรวจเยี่ยมศูนย์พักพิง (ส่งให้ผู้ดูแลภายในศูนย์ที่เกี่ยวข้อง)', 'type' => 1, 'description' => [
                "line_type" => [LineController::Shelter_appointment_update],
                "user" => 0,
            ]],
            ['name' => 'แจ้งเตือน นัดหมายตรวจเยี่ยมศูนย์พักพิงถูกยกเลิก (ส่งให้ผู้ดูแลภายในศูนย์ที่เกี่ยวข้อง)', 'type' => 1, 'description' => [
                "line_type" => [LineController::Shelter_appointment_canceled],
                "user" => 0,
            ]],
            ['name' => 'แจ้งเตือน นัดหมายตรวจเยี่ยมศูนย์พักพิงใหม่ (ส่งให้สมาชิกภายในทีมเจ้าหน้าที่ที่เกี่ยวข้อง)', 'type' => 1, 'description' => [
                "line_type" => [LineController::Team_appointment],
                "user" => 0,
            ]],
            ['name' => 'แจ้งเตือน แก้ไขนัดหมายตรวจเยี่ยมศูนย์พักพิง (ส่งให้สมาชิกภายในทีมเจ้าหน้าที่ที่เกี่ยวข้อง)', 'type' => 1, 'description' => [
                "line_type" => [LineController::Team_appointment_update],
                "user" => 0,
            ]],
            ['name' => 'แจ้งเตือน นัดหมายตรวจเยี่ยมศูนย์พักพิงถูกยกเลิก (ส่งให้สมาชิกภายในทีมเจ้าหน้าที่ที่เกี่ยวข้อง)', 'type' => 1, 'description' => [
                "line_type" => [LineController::Team_appointment_canceled],
                "user" => 0,
            ]],
            ['name' => 'แจ้งเตือน เจ้าหน้าที่ถูก ย้าย / นำเข้า / นำออก ศูนย์พักพิง (ส่งให้เจ้าหน้าที่ที่ถูกย้าย / นำเข้า / นำออก)', 'type' => 1, 'description' => [
                "line_type" => [LineController::User_on, LineController::User_off],
                "user" => 0,
            ]],
            ['name' => 'แจ้งเตือน เจ้าหน้าที่ถูก ย้าย / นำเข้า / นำออก ทีมเจ้าหน้าที่ (ส่งให้เจ้าหน้าที่ที่ถูกย้าย / นำเข้า / นำออก)', 'type' => 1, 'description' => [
                "line_type" => [LineController::User_join, LineController::User_leave],
                "user" => 0,
            ]],
            ['name' => 'แสดงภัยพิบัต', 'type' => 5, 'description' => [
                "flood" => 0,
                "smog" => 0,
                "earthquake" => 0,
                "wildfire" => 0
            ]],
            ['name' => 'lineCredit', 'type' => 6, 'description' => [
                "totalUsage" => 0,
                "limited" => 0,
                "type" => 1, //1 limit 2 unlimit
                "rate" => 0
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
