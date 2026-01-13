<?php

namespace App\Http\Controllers\API;

use App\Enums\FloodSourceType;
use App\Enums\FloodStatus;
use App\Enums\ShiftType;
use App\Enums\SmogStatus;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Http\Utility\DateUtility;
use App\Models\LineUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Config;
use App\Models\Population;
use App\Models\Role;
use App\Models\User;

class LineController extends Controller
{

    #region LINE CONSTANTS
    public const Population = 1;
    public const User = 7;

    public const Disaster_post = 1; // hotline
    public const Disaster_end = 2; // แจ้งเตือน ยุติเหตุภัยพิบัติ
    public const Shelter_post = 3; //แจ้งเตือน เปิดทำการศูนย์พักพิง
    public const Shelter_end = 4; // แจ้งเตือน ปิดทำการศูนย์พักพิง
    public const Damaged_utility_post = 5; //แจ้งเตือน อัปเดตสาธารณประโยชน์ที่ได้รับความเสียหาย
    public const Damaged_utility_end = 6;
    public const Resource = 7;  //แจ้งเตือน คำร้องขอทรัพยากร
    public const News_post = 8; // hotline
    public const News_update = 9; // hotline
    public const Emc_request_post = 10; //แจ้งเตือน คำร้องขอใหม่ 
    public const Emc_request_unaccept = 29; //แจ้งเตือน ปฏิเสธรายการรอช่วยเหลือ (ส่งให้เจ้าหน้าที่ส่วนกลาง (ทุกสถานะ))
    public const Emc_request_post_team = 11; //แจ้งเตือน รายการรอช่วยเหลือใหม่ (ส่งให้หัวหน้าทีมที่เกี่ยวข้องอนุมัติ)
    public const Emc_request_end = 12; // แจ้งเตือน รายการรอช่วยเหลือถูกยกเลิก (ส่งให้หัวหน้าทีมที่เกี่ยวข้อง)
    public const Emc_assigned = 13; // แจ้งเตือน รายการรอช่วยเหลือใหม่ (ส่งให้สมาชิกในทีมที่เกี่ยวข้องเมื่ออนุมัติสำเร็จ)
    public const Emc_success = 14; //แจ้งเตือน รายงานผลการช่วยเหลือ (ส่งให้เจ้าหน้าที่ส่วนกลาง (ทุกสถานะ))
    public const Emc_unsuccess = 23;
    public const User_register = 15; //แจ้งเตือน คำร้องขอลงทะเบียนใหม่
    public const User_on = 16; //แจ้งเตือน เจ้าหน้าที่ถูก ย้าย / นำเข้า / นำออก ศูนย์พักพิง
    public const User_off = 17;
    public const User_join = 18; //แจ้งเตือน เจ้าหน้าที่ถูก ย้าย / นำเข้า / นำออก ทีมเจ้าหน้าที่
    public const User_leave = 19;
    public const Shelter_appointment = 30; //แจ้งเตือน นัดหมายตรวจเยี่ยมศูนย์พักพิงใหม่ 
    public const Shelter_appointment_update = 31; //แจ้งเตือน แก้ไขนัดหมายตรวจเยี่ยมศูนย์พักพิง
    public const Shelter_appointment_canceled = 32; //แจ้งเตือน นัดหมายตรวจเยี่ยมศูนย์พักพิงถูกยกเลิก
    public const Team_appointment = 20; //แจ้งเตือน นัดหมายตรวจเยี่ยมศูนย์พักพิงใหม่ 
    public const Team_appointment_update = 21; //แจ้งเตือน แก้ไขนัดหมายตรวจเยี่ยมศูนย์พักพิง
    public const Team_appointment_canceled = 22; //แจ้งเตือน นัดหมายตรวจเยี่ยมศูนย์พักพิงถูกยกเลิก
    public const Disaster_flood = 24;
    public const Disaster_landslide = 25;
    public const Disaster_wildfire_m1 = 26.1;
    public const Disaster_wildfire_m2 = 26.2;
    public const Disaster_wildfire_m3 = 26.3;
    public const Disaster_wildfire_m4 = 26.4;
    public const Disaster_smog = 27;
    public const Disaster_earthquake = 28;
    public const Hospital = 33;
    private const Hotline = [
        self::Disaster_flood,
        self::Disaster_landslide,
        self::Disaster_wildfire_m1,
        self::Disaster_wildfire_m2,
        self::Disaster_wildfire_m3,
        self::Disaster_wildfire_m4,
        self::Disaster_smog,
        self::Disaster_earthquake,
        self::Disaster_post,
        self::News_post,
        self::News_update,
    ];
    private const DistrictId = [
        self::Disaster_flood,
        self::Disaster_smog,
        self::Disaster_earthquake,
        self::Disaster_post,
    ];
    private const DistrictAll = [
        self::News_post,
        self::News_update
    ];
    #endregion

    public function handle(Request $request)
    {
        if ($request->events) {
            $event = $request->events[0];
            // Log::info($event);
            if ($event['type'] === 'message') {
                $replyToken = $event['replyToken'];
                $text = $event['message']['text'];
                if ($text === 'test') {
                    $message = [
                        'type' => 'flex',
                        'altText' => 'ข้อความแจ้งเตือน',
                        'contents' => [
                            'type' => 'bubble',
                            'body' => [
                                'type' => 'box',
                                'layout' => 'vertical',
                                'contents' => [
                                    [
                                        'type' => 'text',
                                        'text' => __('line.location', ['province' => '123']),
                                        'wrap' => true
                                    ]
                                ]
                            ],
                            'footer' => [
                                'type' => 'box',
                                'layout' => 'vertical',
                                'spacing' => 'sm',
                                'contents' => [
                                    [
                                        'type' => 'button',
                                        'style' => 'link',
                                        'height' => 'sm',
                                        'action' => [
                                            'type' => 'uri',
                                            'label' => 'อ่านข่าวประชาสัมพันธ์',
                                            'uri' => 'https://disaster-backend.gentechsoft.tech/home-page'
                                        ],
                                        'color' => '#FFFFFF'
                                    ]
                                ],
                                'flex' => 0,
                                'backgroundColor' => '#9B237B'
                            ]
                        ]
                    ];

                    $this->broadcast_reply(['replyToken' => $replyToken, 'messages' => [$message]]);
                }
            } else  if ($event['type'] === 'follow') {
                LineUser::create([
                    'token_id' => $event['source']['userId']
                ]);
            } else  if ($event['type'] === 'unfollow') {
                $data = LineUser::where('token_id', $event['source']['userId'])->first();
                if ($data) $data->delete();
            }
        }
        return response()->json(['status' => 'ok']);
    }

    private function limitUpdate($data, $limit)
    {
        $limit['totalUsage'] = $limit['totalUsage'] + count($data);
        Config::where('type', Config::lineCount)
            ->update(['description' => $limit]);
    }

    public function LineNotification(array $data = null, $form, $recipint = null)
    {
        //line limitCount
        $limitCount = Config::where('type', Config::lineCount)->first();
        $limit = $limitCount?->description;
        if ($limit['type'] == Config::limited && ($limit['limited'] <= $limit['totalUsage'])) return;

        if (in_array($form, self::Hotline)) {
            //หา จังหวัด user pop ในระบบ
            if (in_array($form, self::DistrictId)) $recipint = self::areaFind($data['province_name_th'] ?? $data['province_name']);
            if (in_array($form, self::DistrictAll)) $recipint = self::areaFind(null);
            $message = self::form_box($form, $data, self::Population);
            self::broadcast_multi($recipint, $message);
            //เก็บ จำนวนส่ง
            self::limitUpdate($recipint, $limit);
            return response()->json(["success" => true, "message" => 'Send Line OK.', 'data' => $message]);
        }

        $config = Config::WhereJsonContains('description->line_type', $form)
            ->where('type', Config::line)
            ->first();

        if ($recipint) {
            if ($config) { // check config
                if ($config->description['user']) {
                    $message = self::form_box($form, $data, self::User);
                    self::broadcast_multi($recipint, $message);
                    //เก็บ จำนวนส่ง
                    self::limitUpdate($recipint, $limit);
                }
            }
        } else {
            $line = LineUser::all(); // ยังไม่ได้กำหนด เฉพาะกลุ่ม

            if ($config) { // check config
                if ($config->description['user']) {
                    $hasUser = $line
                        ->filter(fn($item) => $item->role === LineUser::User)
                        ->pluck('token_id');

                    if ($hasUser->isNotEmpty()) { // check user empty
                        $message = self::form_box($form, $data, self::User);
                        self::broadcast_multi($hasUser, $message);
                        //เก็บ จำนวนส่ง
                        self::limitUpdate($hasUser, $limit);
                    }
                }
                if (isset($config->description['population'])) {
                    if ($config->description['population']) {
                        $noUser = $line->filter(fn($item) =>
                        $item->role === LineUser::Population)->pluck('token_id');

                        if ($noUser->isNotEmpty()) {
                            $message = self::form_box($form, $data, self::Population);
                            self::broadcast_multi($noUser, $message);
                            //เก็บ จำนวนส่ง
                            self::limitUpdate($noUser, $limit);
                        }
                    }
                }
            }
        }
        return response()->json(["success" => true, "message" => 'Send Line OK.']);
    }

    private function areaFind($province = null, $select = 'all') //select : 1 : user , 2 : pop , null : all
    {
        $recipint = collect();

        if ($select == 1 || $select == 'all') {
            $userTokens = User::select('line_users.token_id as token')
                ->leftJoin('geography', 'users.district_id', 'geography.district_code')
                ->leftJoin('line_users', 'users.id', 'line_users.user_id')
                ->where('line_users.role', LineUser::User)
                ->when(
                    $province,
                    fn($q) => $q->where('geography.province_name_th', $province)
                )
                // ->where('geography.province_name_th', $province)
                ->whereNotNull('line_users.token_id')
                ->distinct()
                ->pluck('token'); // เอาแค่ค่า ไม่ต้อง get()->keyBy()->keys()

            $recipint = $recipint->merge($userTokens);
        }

        if ($select == 2 || $select == 'all') {
            // $populationTokens = LineUser::select('line_users.token_id as token')
            //     ->leftJoin('populations', 'line_users.user_id', 'populations.id')
            //     ->leftJoin('geography', 'populations.p_geography_id', 'geography.id')
            //     ->where('line_users.role', LineUser::Population)
            //     ->whereNotNull('line_users.token_id')
            //     ->where(function ($q) use ($province) {
            //         $q->where('line_users.province', $province)
            //             ->orWhere(function ($q2) use ($province) {
            //                 $q2->whereNull('line_users.province')
            //                     ->where('geography.province_name_th', $province);
            //             });
            //     })
            //     ->distinct()
            //     ->pluck('token');

            $populationTokens = LineUser::select('line_users.token_id as token')
                ->leftJoin('populations', 'line_users.user_id', 'populations.id')
                ->leftJoin('geography', 'populations.p_geography_id', 'geography.id')
                ->where('line_users.role', LineUser::Population)
                ->whereNotNull('line_users.token_id')
                ->when($province, function ($q) use ($province) {
                    $q->where(function ($q2) use ($province) {
                        $q2->where('line_users.province', $province)
                            ->orWhere(function ($q3) use ($province) {
                                $q3->whereNull('line_users.province')
                                    ->where('geography.province_name_th', $province);
                            });
                    });
                })
                ->distinct()
                ->pluck('token');


            $recipint = $recipint->merge($populationTokens);
        }

        return $recipint->unique()->values();
    }

    private function form_box($form, $data = null, $type = null)
    {
        if ($form == self::Disaster_post) {
            $text = __('line.disaster_post', [
                'type' => $data['type'],
                'level' => $data['level'],
                'type_areas' => $data['areas'],
            ]) . __('line.close_follow');
            $text = self::flex_box($text);
        } else if ($form == self::Disaster_end) {
            $text = __('line.disaster_end',  [
                'type' => $data['type'],
                'level' => $data['level'],
                'type_areas' => $data['areas'],
            ]) . __('line.close_follow');
            $text = self::flex_box($text);
        } else if ($form == self::Shelter_post) {
            $text = __('line.shelter_post', ['shelter' => $data['name']]);
            $text = self::flex_box($text, $data['gis_map'] ?? '');
        } else if ($form == self::Shelter_end) {
            $text = __('line.shelter_end', ['shelter' => $data['name']]) . __('line.close_follow');
            $text = self::flex_box($text);
        } else if ($form == self::Damaged_utility_post) {
            $text = __('line.damaged_utilities_head')
                . __('line.damaged_utilities_detail_post', [
                    'utility' => $data['type_name'],
                    'utility_name' => $data['name'],
                ])
                . __('line.close_follow');
            $text = self::flex_box($text);
        } else if ($form == self::Damaged_utility_end) {
            $text = __('line.damaged_utilities_head')
                . __('line.damaged_utilities_detail_end', [
                    'utility' => $data['type_name'],
                    'utility_name' => $data['name'],
                ])
                . __('line.close_follow');
            $text = self::flex_box($text);
        } else if ($form == self::Resource) {
            $text = __('line.resource', [
                'shelter' => $data['shelter'],
                'product' => !empty($data['product'])
                    ? "• " . implode("\n • ", $data['product'])
                    : '',
            ]) . __('line.close_follow');
            $text = self::flex_box_link($text, self::Resource, __('line.see_more'), $type);
        } else if ($form == self::News_post) {
            $text = __('line.news_post', [
                'name' => $data['title'],
            ]);
            $text = self::flex_box_link($text, self::News_post, __('line.read_more'), $type);
        } else if ($form == self::News_update) {
            $text = __('line.news_update', [
                'name' => $data['title'],
            ]);
            $text = self::flex_box_link($text, self::News_update, __('line.read_more'), $type);
        } else if ($form == self::Emc_request_post) {
            $text = __('line.emc_request_post', [
                'name' => $data['name'],
            ]) . __('line.quick');
            $text = self::flex_box_link($text, self::Emc_request_post, __('line.see_request'), $type);
        } else if ($form == self::Emc_request_unaccept) {
            $text = __('line.emc_request_unaccept', [
                'name' => $data['name'],
                'team' => !empty($data['team'] ?? [])
                    ? "• " . implode("\n • ", $data['team'])
                    : '',
                'assigner' => $data['assigner'],
            ]) . __('line.quick');
            $text = self::flex_box_link($text, self::Emc_request_post_team, __('line.see_request'), $type);
        } else if ($form == self::Emc_request_post_team) {
            $text = __('line.emc_request_post_team', [
                'name' => $data['name'],
                'team' => !empty($data['team'] ?? [])
                    ? "• " . implode("\n • ", $data['team'])
                    : '',
                'assigner' => $data['assigner'],
            ]) . __('line.quick');
            $text = self::flex_box_link($text, self::Emc_request_post_team, __('line.see_request'), $type);
        } else if ($form == self::Emc_request_end) {
            $text = __('line.emc_request_end', [
                'name' => $data['name'],
                'team' => !empty($data['team'] ?? [])
                    ? "• " . implode("\n • ", $data['team'])
                    : '',
                'assigner' => $data['assigner'],
            ]);
            $text = self::flex_box($text);
        } else if ($form == self::Emc_assigned) {
            $text = __('line.emc_assigned', [
                'name' => $data['name'],
                'team' => !empty($data['team'] ?? [])
                    ? "• " . implode("\n • ", $data['team'])
                    : '',
                'assigner' => $data['assigner'],
                'receiver' => $data['receiver'],
            ]) . __('line.quick');
            $text = self::flex_box_link($text, self::Emc_assigned, __('line.see_request'), $type);
        } else if ($form == self::Emc_success || $form == self::Hospital) {
            $text = __('line.emc_success', [
                'name' => $data['name'],
                'team' => !empty($data['team'] ?? [])
                    ? "• " . implode("\n • ", $data['team'])
                    : '',
                'assigner' => $data['assigner'],
                'receiver' => $data['receiver'],
                'status' => $data['status'],
                'note' => $data['note'] ?? '-',
            ]);
            $text = self::flex_box_link($text, self::Emc_success, __('line.see_request'), $type);
        } else if ($form == self::Emc_unsuccess) {
            $text = __('line.emc_success', [
                'name' => $data['name'],
                'team' => !empty($data['team'] ?? [])
                    ? "• " . implode("\n • ", $data['team'])
                    : '',
                'assigner' => $data['assigner'],
                'receiver' => $data['receiver'],
                'status' => $data['status'],
                'note' => $data['note'] ?? '-',
            ]);
            $text = self::flex_box_link($text, self::Emc_unsuccess, __('line.see_request'), $type);
        } else if ($form == self::User_register) {
            $text = __('line.user_register');
            $text = self::flex_box($text);
        } else if ($form == self::User_on) {
            $text = __('line.user_on', [
                'shelter' => $data['shelter'],
            ]);
            $text = self::flex_box($text);
        } else if ($form == self::User_off) {
            $text = __('line.user_off', [
                'shelter' => $data['shelter'],
            ]);
            $text = self::flex_box($text);
        } else if ($form == self::User_join) {
            $text = __('line.user_join', [
                'team' => $data['team'],
            ]);
            $text = self::flex_box($text);
        } else if ($form == self::User_leave) {
            $text = __('line.user_leave', [
                'team' => $data['team'],
            ]);
            $text = self::flex_box($text);
        } else if ($form == self::Shelter_appointment) {
            // $data = [
            //     'shelter' => 'sh',
            //     'date' => '30 สค 2468',
            //     'time' => 1,
            //     'team' => '55',
            // ];
            $text = __('line.shelter_appointment', [
                'shelter' => $data['shelter'],
                'date' => $data['date'],
                'time' => $data['time'],
                'team' => $data['team'],
            ]);
            $text = self::flex_box($text);
        } else if ($form == self::Shelter_appointment_update) {
            // $data = [
            //     'shelter' => 'xxx x x1',
            //     'old_date' => '2015-03-11',
            //     'old_time' => 1,
            //     'old_team' => 'xxxx',
            //     'new_date' => '2015-07-15',
            //     'new_time' => 1,
            //     'new_team' => '5xxxx25',
            // ];
            $text = __('line.shelter_appointment_update', [
                'shelter' => $data['shelter'],
                'old_date' => $data['old_date'],
                'old_time' => $data['old_time'],
                'old_team' => $data['old_team'],
                'new_date' => $data['new_date'],
                'new_time' => $data['new_time'],
                'new_team' => $data['new_team'],
            ]);
            $text = self::flex_box($text);
        } else if ($form == self::Shelter_appointment_canceled) {
            // $data = [
            //     'shelter' => 'xxx x x1',
            //     'date' => '2015-03-11',
            //     'time' => 1,
            //     'team' => 'xxxx',
            // ];
            $text = __('line.shelter_appointment_canceled', [
                'shelter' => $data['shelter'],
                'date' => $data['date'],
                'time' => $data['time'],
                'team' => $data['team'],
            ]);
            $text = self::flex_box($text);
        } else if ($form == self::Team_appointment) {
            // $data = [
            //     'shelter' => 'sh',
            //     'date' => '30 สค 2468',
            //     'time' => 1,
            //     'team' => '55',
            // ];
            $text = __('line.team_appointment', [
                'shelter' => $data['shelter'],
                'date' => $data['date'],
                'time' => $data['time'],
                'team' => $data['team'],
            ]);
            $text = self::flex_box($text);
        } else if ($form == self::Team_appointment_update) {
            // $data = [
            //     'team' => 'xxx x x1',
            //     'old_date' => '2015-03-11',
            //     'old_time' => 1,
            //     'old_shelter' => 'xxxx',
            //     'new_date' => '2015-07-15',
            //     'new_time' => 1,
            //     'new_shelter' => '5xxxx25',
            // ];
            $text = __('line.team_appointment_update', [
                'team' => $data['team'],
                'old_date' => $data['old_date'],
                'old_time' => $data['old_time'],
                'old_shelter' => $data['old_shelter'],
                'new_date' => $data['new_date'],
                'new_time' => $data['new_time'],
                'new_shelter' => $data['new_shelter'],
            ]);
            $text = self::flex_box($text);
        } else if ($form == self::Team_appointment_canceled) {
            // $data = [
            //     'shelter' => 'xxx x x1',
            //     'date' => '2015-03-11',
            //     'time' => 1,
            //     'team' => 'xxxx',
            // ];
            $text = __('line.team_appointment_canceled', [
                'shelter' => $data['shelter'],
                'date' => $data['date'],
                'time' => $data['time'],
                'team' => $data['team'],
            ]);
            $text = self::flex_box($text);
        } else if ($form == self::Disaster_flood) {
            $text = __('line.disaster_flood', [
                'water_source' => $data['water_source'],
                // 'water_source_type' => $data['water_source_type'],
                'water_source_type' => FloodSourceType::tryFrom($data['water_source_type'])->label(),
                'address' => $data['address'],
                'water_value' => $data['water_value'],
                'status' => FloodStatus::from($data['status'])->label(),
            ]) . __('line.close_follow');
            $text = self::flex_box($text);
        } else if ($form == self::Disaster_landslide) {
            $text = __('line.disaster_landslide', [
                'name' => $data['name'],
            ]) . __('line.close_follow');
            $text = self::flex_box_link($text, self::Disaster_landslide, __('line.see_more'), $type);
        } else if ($form == self::Disaster_wildfire_m1) {
            $text = __('line.disaster_wildfire_h') . __('line.disaster_wildfire_m34', [
                'type' => 'VIIRS',
                'val1' => $data[0] ?? 0,
                'val2' => $data[1] ?? 0,
                'val3' => $data[2] ?? 0,
                'val4' => $data[3] ?? 0,
                'val5' => $data[4] ?? 0,
                'val6' => $data[5] ?? 0,
            ]) . __('line.close_follow');
            $text = self::flex_box($text);
        } else if ($form == self::Disaster_wildfire_m2) {
            $text = __('line.disaster_wildfire_h')
                . "ข้อมูล " . count($data) . " อันดับ จุดความร้อน\n";

            foreach ($data as $item) {
                $text = $text . __('line.disaster_wildfire_m2', [
                    'district' => "• " . $item['district_name'],
                    'viirs' => $item['viirs'],
                ]);
            }
            $text = $text . __('line.close_follow');
            $text = self::flex_box($text);
        } else if ($form == self::Disaster_wildfire_m3) {
            $text = __('line.disaster_wildfire_h') . __('line.disaster_wildfire_m34', [
                'type' => 'MODIS',
                'val1' => $data[0] ?? 0,
                'val2' => $data[1] ?? 0,
                'val3' => $data[2] ?? 0,
                'val4' => $data[3] ?? 0,
                'val5' => $data[4] ?? 0,
                'val6' => $data[5] ?? 0,
            ]) . __('line.close_follow');
            $text = self::flex_box($text);
        } else if ($form == self::Disaster_wildfire_m4) {
            $text = __('line.disaster_wildfire_h') . __('line.disaster_wildfire_m34', [
                'type' => 'VIIRS',
                'val1' => $data[0] ?? 0,
                'val2' => $data[1] ?? 0,
                'val3' => $data[2] ?? 0,
                'val4' => $data[3] ?? 0,
                'val5' => $data[4] ?? 0,
                'val6' => $data[5] ?? 0,
            ]) . __('line.close_follow');
            $text = self::flex_box($text);
        } else if ($form == self::Disaster_smog) {
            $text = __('line.disaster_smog', [
                'source' => $data['source'],
                'address' => $data['address'],
                'smog_value' => $data['smog_value'],
                'status' => SmogStatus::tryFrom($data['status'])->label(),
            ]) . __('line.close_follow');
            $text = self::flex_box($text);
        } else if ($form == self::Disaster_earthquake) {
            $text =  __('line.disaster_earthquake', [
                'address' => $data['address'],
                'occurrence_time' => DateUtility::formFormat1($data['occurrence_time']),
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'magnitude' => $data['magnitude'],
                'depth' => $data['depth'],
            ]) . __('line.close_follow');
            $text = self::flex_box($text);
        }
        return $text;
    }

    private function broadcast_reply($messages)
    {
        $url = 'https://api.line.me/v2/bot/message/reply';

        Http::withToken(env('LINE_BOT_CHANNEL_ACCESS_TOKEN'))
            ->post($url, $messages);
    }

    private function broadcast_push($userId, $message) // one userId
    {
        $limitCount = Config::where('type', Config::lineCount)->first();
        $limit = $limitCount->description;
        if ($limit['type'] == Config::limited && ($limit['limited'] <= $limit['totalUsage'])) return;
        self::limitUpdate([$userId], $limit);

        Http::withToken(env('LINE_BOT_CHANNEL_ACCESS_TOKEN'))->post('https://api.line.me/v2/bot/message/push', [
            'to' => $userId,
            'messages' => [
                [
                    'type' => 'text',
                    'text' => $message,
                ]
            ],
        ]);
    }

    // private function broadcast_all($message) // to all 
    // {
    //     Http::withToken(env('LINE_BOT_CHANNEL_ACCESS_TOKEN'))->post('https://api.line.me/v2/bot/message/broadcast', [
    //         'messages' => [$message],
    //     ]);
    // }

    private function broadcast_multi($userIds, $message) // array userId
    {
        Http::withToken(env('LINE_BOT_CHANNEL_ACCESS_TOKEN'))->post('https://api.line.me/v2/bot/message/multicast', [
            'to' => $userIds,
            'messages' => [$message],
        ]);
    }

    private function flex_box_link($text, $link, $btn, $type)
    {
        return [
            'type' => 'flex',
            'size' => 'giga',
            'altText' => '📢 ข่าวประชาสัมพันธ์',
            'contents' => [
                'type' => 'bubble',
                'body' => [
                    'type' => 'box',
                    'layout' => 'vertical',
                    'contents' => [
                        [
                            'type' => 'text',
                            'text' => $text,
                            'wrap' => true
                        ]
                    ]
                ],
                'footer' => [
                    'type' => 'box',
                    'layout' => 'vertical',
                    'spacing' => 'sm',
                    'contents' => [
                        [
                            'type' => 'button',
                            'style' => 'link',
                            'height' => 'sm',
                            'action' => [
                                'type' => 'uri',
                                'label' => $btn,
                                'uri' => env('APP_URL') . '/api/line-param/' . $link . '?type=' . $type,
                            ],
                            'color' => '#FFFFFF'
                        ]
                    ],
                    'flex' => 0,
                    'backgroundColor' => '#9B237B'
                ]
            ]
        ];
    }

    private function flex_box($text, $local = null)
    {
        if (!$local) {
            return [
                'type' => 'flex',
                'size' => 'giga',
                'altText' => '📢 ข่าวประชาสัมพันธ์',
                'contents' => [
                    'type' => 'bubble',
                    'body' => [
                        'type' => 'box',
                        'layout' => 'vertical',
                        'contents' => [
                            [
                                'type' => 'text',
                                'text' => $text,
                                'wrap' => true
                            ]
                        ]
                    ]
                ]
            ];
        } else {
            return [
                'type' => 'flex',
                'size' => 'giga',
                'altText' => '📢 ข่าวประชาสัมพันธ์',
                'contents' => [
                    'type' => 'bubble',
                    'body' => [
                        'type' => 'box',
                        'layout' => 'vertical',
                        'contents' => [
                            [
                                'type' => 'text',
                                'text' => $text,
                                'wrap' => true
                            ],
                            [
                                'type' => 'text',
                                'text' => '🗺️ ดูแผนที่ศูนย์พักพิง',
                                'color' => '#1E90FF',
                                'action' => [
                                    'type' => 'uri',
                                    'label' => 'ดูแผนที่',
                                    'uri' => $local
                                ]
                            ],
                            [
                                'type' => 'text',
                                'text' => "\n" . __('line.close_follow'),
                                'wrap' => true
                            ],
                        ]
                    ]
                ]
            ];
        }
    }

    public function user_login($userId, $text) //push text and change rich to user
    {
        self::broadcast_push($userId, $text);
        $this->rich_change($userId, env('RICHMENU_AUTH'));
    }

    private function rich_change($userId, $richId)
    {
        Http::withToken(env('LINE_BOT_CHANNEL_ACCESS_TOKEN'))
            ->post('https://api.line.me/v2/bot/user/' . $userId . '/richmenu/' . $richId);
    }

    public function chanel(Request $request, $chanel)
    {
        // return response()->json(['success' => true, 'message' => $request->type]);
        $type = $request->type;

        if ($chanel == self::Resource) {
            if ($type == self::User) return redirect('/dashboard');
            return redirect('/home-page');
        } else if ($chanel == self::News_post || $chanel == self::News_update) {
            return redirect('/home-page');
        } else if ($chanel == self::Emc_request_post) {
            return redirect('/emergency-requests/requests');
        } else if ($chanel == self::Emc_request_post_team || $chanel == self::Emc_assigned) {
            return redirect('/emergency-requests/waiting-lists-for-help');
        } else if ($chanel == self::Emc_success) {
            return redirect('/emergency-requests/waiting-lists-for-help');
        } else if ($chanel == self::Emc_unsuccess) {
            return redirect('/emergency-requests/waiting-lists-for-help');
        } else if ($chanel == self::Disaster_landslide) {
            return redirect('/landslide');
        }
    }



    public function getAdminLineIds(): array
    {
        return LineUser::whereHas('user.roles', function ($q) {
            $q->where('name', Role::ADMIN);
        })->pluck('token_id')->toArray();
    }

    public function getStaffLineIds($shelterId): array
    {
        return LineUser::whereHas('staff', function ($query) use ($shelterId) {
            $query->where('shelter_id', $shelterId);
        })->pluck('token_id')->toArray();
        // return LineUser::with('staff')->get()->toArray();
    }

    public function getTeamLineIds($teamId): array
    {
        return LineUser::whereHas('team', function ($query) use ($teamId) {
            $query->where('team_id', $teamId);
        })->pluck('token_id')->toArray();
        // return LineUser::with('team')->get()->toArray();
    }

    public function gps(Request $r)
    {
        $q = LineUser::where('token_id', $r->userId)->first();
        if ($q) {
            $q->update([
                'latitude' => $r->latitude,
                'longitude' => $r->longitude,
                'province' => $r->province,
                'role' => 2
            ]);
            self::broadcast_push($r->userId, __('line.location', ['province' => $r->province]));
            return ApiResponse::successResponse(__('success.data'));
        }
        return ApiResponse::errorResponse(__('error.model_not_found'));
    }
}
