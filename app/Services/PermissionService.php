<?php

namespace App\Services;

use App\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionService
{
    public function create(array $data)
    {
        $roles = [];
        $roles[] = Role::create(['name' => Role::ADMIN]);
        $roles[] = Role::create(['name' => Role::STAFF]);
        $roles[] = Role::create(['name' => Role::TEAM_LEADER]);
        $roles[] = Role::create(['name' => Role::TEAM_MEMBER]);
        return $roles;
    }
    // public function createPermission(array $data)
    // {
    //     $permissionList = [
    //         [
    //             'name_th' => 'ประกาศภัยพิบัติ',
    //             'name_en' => 'update-disasters'
    //         ],
    //         [
    //             'name_th' => 'แก้ไขข้อมูลอุทกภัย',
    //             'name_en' => 'update-floods'
    //         ],
    //         [
    //             'name_th' => 'แก้ไขข้อมูลดินถล่ม',
    //             'name_en' => 'update-landslides'
    //         ],
    //         [
    //             'name_th' => 'ลบข้อมูลดินถล่ม',
    //             'name_en' => 'delete-landslides'
    //         ],
    //         [
    //             'name_th' => 'แก้ไขข้อมูลไฟป่า',
    //             'name_en' => 'update-wildfires'
    //         ],
    //         [
    //             'name_th' => 'แก้ไขข้อมูลหมอกควัน',
    //             'name_en' => 'update-smogs'
    //         ],
    //         [
    //             'name_th' => 'แก้ไขข้อมูลพายุฤดูร้อน',
    //             'name_en' => 'update-thunderstorms'
    //         ],
    //         [
    //             'name_th' => 'แก้ไขข้อมูลแผ่นดินไหว',
    //             'name_en' => 'update-earthquakes'
    //         ],
    //         [
    //             'name_th' => 'แก้ไขข้อมูลอัคคีภัย',
    //             'name_en' => 'update-fires'
    //         ],
    //         [
    //             'name_th' => 'แก้ไขข้อมูลโรคระบาด',
    //             'name_en' => 'update-epidemics'
    //         ],
    //         [
    //             'name_th' => 'ดูข้อมูลศูนย์พักพิง',
    //             'name_en' => 'view-shelters'
    //         ],
    //         [
    //             'name_th' => 'สร้างศูนย์พักพิง',
    //             'name_en' => 'create-shelters'
    //         ],
    //         [
    //             'name_th' => 'แก้ไขข้อมูลศูนย์พักพิง',
    //             'name_en' => 'update-shelters'
    //         ],
    //         [
    //             'name_th' => 'ลบข้อมูลศูนย์พักพิง',
    //             'name_en' => 'delete-shelters'
    //         ],
    //         [
    //             'name_th' => 'ย้ายเข้าสู่ศูนย์',
    //             'name_en' => 'create-staff'
    //         ],
    //         [
    //             'name_th' => 'ย้ายเข้าสู่ศูนย์',
    //             'name_en' => 'join-shelter'
    //         ],
    //         [
    //             'name_th' => 'ออกจากศูนย์',
    //             'name_en' => 'exit-shelter'
    //         ],
    //         [
    //             'name_th' => 'ออกจากศูนย์',
    //             'name_en' => 'exit-all-shelter'
    //         ],
    //         [
    //             'name_th' => 'แกัไชตำแหน่งงานภายในศูนย์',
    //             'name_en' => 'assign-staff-position'
    //         ],
    //         [
    //             'name_th' => 'ดูข้อมูลข่าวประชาสัมพันธ์ภายในศูนย์พักพิง',
    //             'name_en' => 'view-shelter-news'
    //         ],
    //         [
    //             'name_th' => 'สร้างข้อมูลข่าวประชาสัมพันธ์ภายในศูนย์พักพิง',
    //             'name_en' => 'create-shelter-news'
    //         ],
    //         [
    //             'name_th' => 'แก้ไขข้อมูลข่าวประชาสัมพันธ์ภายในศูนย์พักพิง',
    //             'name_en' => 'update-shelter-news'
    //         ],
    //         [
    //             'name_th' => 'ลบข้อมูลข่าวประชาสัมพันธ์ภายในศูนย์พักพิง',
    //             'name_en' => 'delete-shelter-news'
    //         ],
    //         [
    //             'name_th' => 'ดูข้อมูลทีมเจ้าหน้าที่',
    //             'name_en' => 'view-teams'
    //         ],
    //         [
    //             'name_th' => 'สร้างทีมจ้าหน้าที่',
    //             'name_en' => 'create-teams'
    //         ],
    //         [
    //             'name_th' => 'แก้ไขทีมจ้าหน้าที่',
    //             'name_en' => 'update-teams'
    //         ],
    //         [
    //             'name_th' => 'ลบทีมจ้าหน้าที่',
    //             'name_en' => 'delete-teams'
    //         ],
    //         [
    //             'name_th' => 'ดูข้อมูลนัดหมาย',
    //             'name_en' => 'view-appointments'
    //         ],
    //         [
    //             'name_th' => 'สร้างนัดหมาย',
    //             'name_en' => 'create-appointments'
    //         ],
    //         [
    //             'name_th' => 'แก้ไขนัดหมาย',
    //             'name_en' => 'update-appointments'
    //         ],
    //         [
    //             'name_th' => 'ลบนัดหมาย',
    //             'name_en' => 'delete-appointments'
    //         ],
    //         [
    //             'name_th' => 'แก้ไขข้อมูลประวัติการนัดหมาย',
    //             'name_en' => 'update-appointment-logs'
    //         ],
    //         [
    //             'name_th' => 'ดูข้อมูลคำร้องขอ',
    //             'name_en' => 'view-emc-requests'
    //         ],
    //         [
    //             'name_th' => 'แก้ไขคำร้องขอ',
    //             'name_en' => 'update-emc-requests'
    //         ],
    //         [
    //             'name_th' => 'ลบคำร้องขอ',
    //             'name_en' => 'delete-emc-requests'
    //         ],
    //         [
    //             'name_th' => 'ดูข้อมูลประชากร',
    //             'name_en' => 'view-populations'
    //         ],
    //         [
    //             'name_th' => 'สร้างประชากร',
    //             'name_en' => 'create-populations'
    //         ],
    //         [
    //             'name_th' => 'แก้ไขประชากร',
    //             'name_en' => 'update-populations'
    //         ],
    //         [
    //             'name_th' => 'ลบประชากร',
    //             'name_en' => 'delete-populations'
    //         ],
    //         [
    //             'name_th' => 'ดูข้อมูลเจ้าหน้าที่',
    //             'name_en' => 'view-users'
    //         ],
    //         [
    //             'name_th' => 'สร้างเจ้าหน้าที่',
    //             'name_en' => 'create-users'
    //         ],
    //         [
    //             'name_th' => 'แก้ไขเจ้าหน้าที่',
    //             'name_en' => 'update-users'
    //         ],
    //         [
    //             'name_th' => 'ลบเจ้าหน้าที่',
    //             'name_en' => 'delete-users'
    //         ],
    //         [
    //             'name_th' => 'อนุมัติเจ้าหน้าที่',
    //             'name_en' => 'approve-users'
    //         ],
    //         [
    //             'name_th' => 'เข้าร่วมทีม',
    //             'name_en' => 'join-team'
    //         ],
    //         [
    //             'name_th' => 'ย้ายทีม',
    //             'name_en' => 'transfer-team'
    //         ],
    //         [
    //             'name_th' => 'ออกจากทีม',
    //             'name_en' => 'leave-team'
    //         ],
    //         [
    //             'name_th' => 'ดูข้อมูลทรัพยากรกลาง',
    //             'name_en' => 'view-products'
    //         ],
    //         [
    //             'name_th' => 'สร้างทรัพยากรกลาง',
    //             'name_en' => 'create-products'
    //         ],
    //         [
    //             'name_th' => 'แก้ไขทรัพยากรกลาง',
    //             'name_en' => 'update-products'
    //         ],
    //         [
    //             'name_th' => 'ลบทรัพยากรกลาง',
    //             'name_en' => 'delete-products'
    //         ],
    //         [
    //             'name_th' => 'ดูข้อมูลทรัพยากรภายในศูนย์',
    //             'name_en' => 'view-shelter-products'
    //         ],
    //         [
    //             'name_th' => 'สร้างทรัพยากรภายในศูนย์',
    //             'name_en' => 'create-shelter-products'
    //         ],
    //         [
    //             'name_th' => 'แก้ไขทรัพยากรภายในศูนย์',
    //             'name_en' => 'update-shelter-products'
    //         ],
    //         [
    //             'name_th' => 'ลบทรัพยากรภายในศูนย์',
    //             'name_en' => 'delete-shelter-products'
    //         ],
    //         [
    //             'name_th' => 'จัดการตั้งค่าการแจ้งเตือน',
    //             'name_en' => 'update-configs'
    //         ],
    //         [
    //             'name_th' => 'ดูข้อมูลข่าวประชาสัมพันธ์',
    //             'name_en' => 'view-news'
    //         ],
    //         [
    //             'name_th' => 'สร้างข่าวประชาสัมพันธ์',
    //             'name_en' => 'create-news'
    //         ],
    //         [
    //             'name_th' => 'แก้ไขข่าวประชาสัมพันธ์',
    //             'name_en' => 'update-news'
    //         ],
    //         [
    //             'name_th' => 'ลบข่าวประชาสัมพันธ์',
    //             'name_en' => 'delete-news'
    //         ],
    //     ];

    //     foreach($permissionList as $permission){
    //         Permission::create(['name' => $permission['name_en']]);
    //     }
    // }

    public function getRoles()
    {
        return Role::all()->select('id', 'name');
    }

    public function getPermissions()
    {
        return Permission::all()->select('id', 'name');
    }
}
