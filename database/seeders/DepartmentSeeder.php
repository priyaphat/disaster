<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $datas = [
            'เจ้าหน้าที่ศูนย์บัญชาการ',
            'ส่วนอำนวยการ',
            'ส่วนสนับสนุน',
            'ส่วนปฏิบัติการ'
        ];

        foreach ($datas as $data) {
            Department::create(['name' => $data]);
        }
    }
}
