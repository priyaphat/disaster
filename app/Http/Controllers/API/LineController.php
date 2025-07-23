<?php

namespace App\Http\Controllers\API;

use App\Enums\FloodStatus;
use App\Enums\ShiftType;
use App\Enums\SmogStatus;
use App\Http\Controllers\Controller;
use App\Http\Utility\DateUtility;
use App\Models\LineUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Config;
use App\Models\Population;
use App\Models\User;

class LineController extends Controller
{

    #region LINE CONSTANTS
    public const Population = 1;
    public const User = 7;

    public const Disaster_post = 1; // hotline success
    public const Disaster_end = 2; // แจ้งเตือน ยุติเหตุภัยพิบัติ success
    public const Shelter_post = 3; //แจ้งเตือน เปิดทำการศูนย์พักพิง success
    public const Shelter_end = 4; // แจ้งเตือน ปิดทำการศูนย์พักพิง success
    public const Damaged_utility_post = 5; //แจ้งเตือน อัปเดตสาธารณประโยชน์ที่ได้รับความเสียหาย ok
    public const Damaged_utility_end = 6; // ok 
    public const Resource = 7;  //แจ้งเตือน คำร้องขอทรัพยากร
    public const News_post = 8; // hotline ok
    public const News_update = 9; // hotline ok
    public const Emc_request_post = 10; //แจ้งเตือน คำร้องขอใหม่ req id  ok
    public const Emc_request_unaccept = 29; //แจ้งเตือน ปฏิเสธรายการรอช่วยเหลือ (ส่งให้เจ้าหน้าที่ส่วนกลาง (ทุกสถานะ)) req id ok
    public const Emc_request_post_team = 11; //แจ้งเตือน รายการรอช่วยเหลือใหม่ (ส่งให้หัวหน้าทีมที่เกี่ยวข้องอนุมัติ) req id ok
    public const Emc_request_end = 12; // แจ้งเตือน รายการรอช่วยเหลือถูกยกเลิก (ส่งให้หัวหน้าทีมที่เกี่ยวข้อง) req id
    public const Emc_assigned = 13; // แจ้งเตือน รายการรอช่วยเหลือใหม่ (ส่งให้สมาชิกในทีมที่เกี่ยวข้องเมื่ออนุมัติสำเร็จ) req id ok
    public const Emc_success = 14; //แจ้งเตือน รายงานผลการช่วยเหลือ (ส่งให้เจ้าหน้าที่ส่วนกลาง (ทุกสถานะ)) req id ok
    public const Emc_unsuccess = 23; // ok
    public const User_register = 15; //แจ้งเตือน คำร้องขอลงทะเบียนใหม่ req id ok
    public const User_on = 16; //แจ้งเตือน เจ้าหน้าที่ถูก ย้าย / นำเข้า / นำออก ศูนย์พักพิง req id success
    public const User_off = 17; // success
    public const User_join = 18; //แจ้งเตือน เจ้าหน้าที่ถูก ย้าย / นำเข้า / นำออก ทีมเจ้าหน้าที่ req id ok
    public const User_leave = 19; //ok
    public const Appointment = 20; //แจ้งเตือน นัดหมายตรวจเยี่ยมศูนย์พักพิงใหม่ req id
    public const Appointment_update = 21; //แจ้งเตือน แก้ไขนัดหมายตรวจเยี่ยมศูนย์พักพิง req id
    public const Appointment_canceled = 22; //แจ้งเตือน นัดหมายตรวจเยี่ยมศูนย์พักพิงถูกยกเลิก req id
    public const Disaster_flood = 24;
    public const Disaster_landslide = 25;
    public const Disaster_wildfire_m1 = 26.1;
    public const Disaster_wildfire_m2 = 26.2;
    public const Disaster_wildfire_m3 = 26.3;
    public const Disaster_wildfire_m4 = 26.4;
    public const Disaster_smog = 27;
    public const Disaster_earthquake = 28;
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
                        'contents' => [
                            'type' => 'bubble',
                            'body' => [
                                'type' => 'box',
                                'layout' => 'vertical',
                                'contents' => [
                                    [
                                        'type' => 'text',
                                        'text' => __('line.disaster_end'),
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

    public function LineNotification(array $data = null, $form, $recipint = null)
    {

        if (in_array($form, self::Hotline)) {
            //หา จังหวัด user pop ในระบบ
            // return self::areaFind('เชียงราย'); //$recipint =

            $message = self::form_box($form, $data, self::Population);
            self::broadcast_multi($recipint, $message);
            return response()->json(["success" => true, "message" => 'Send Line OK.', 'data' => $message]);
        }
        // return 'x'; //interapt

        $config = Config::WhereJsonContains('description->line_type', $form)
            ->where('type', 1)
            ->first();

        if ($recipint) {
            if ($config) { // check config
                if ($config->description['user']) {
                    $message = self::form_box($form, $data, self::User);
                    self::broadcast_multi($recipint, $message);
                }
            }
        } else {
            $line = LineUser::all(); // ยังไม่ได้กำหนด เฉพาะกลุ่ม

            if ($config) { // check config
                if ($config->description['user']) {
                    $hasUser = $line
                        ->filter(fn($item) => !is_null($item->user_id))
                        ->pluck('token_id');

                    if ($hasUser->isNotEmpty()) { // check user empty
                        $message = self::form_box($form, $data, self::User);
                        self::broadcast_multi($hasUser, $message);
                    }
                }
                if (isset($config->description['population'])) {
                    if ($config->description['population']) {
                        $noUser = $line->filter(fn($item) =>
                        is_null($item->user_id))->pluck('token_id');

                        if ($noUser->isNotEmpty()) {
                            $message = self::form_box($form, $data, self::Population);
                            self::broadcast_multi($noUser, $message);
                        }
                    }
                }
            }
        }
        return response()->json(["success" => true, "message" => 'Send Line OK.']);
    }

    private function areaFind($province, $select = 'all') //select : 1 : user , 2 : pop , null : all
    {
        $recipint = [];

        if ($select == 1 || $select == 'all') {
            $recipint = User::select(
                // 'users.*', 'geography.province_name_th', 
                'line_users.token_id as token'
            )
                ->leftJoin('geography', 'users.district_id', 'geography.district_code')
                ->leftJoin('line_users', 'users.id', 'line_users.user_id')
                ->where('geography.province_name_th', $province)
                ->whereNotNull('line_users.token_id')
                ->distinct()->get()->keyBy('token')->keys();
        }
        if ($select == 2 || $select == 'all') {
            // $population = Population::get(); // ไม่สามารถหาที่อยู่ของประชาชนได้ ไม่ได้จับคู่
        }

        return $recipint;
    }

    private function form_box($form, $data = null, $type = null)
    {
        if ($form == self::Disaster_post) {
            $text = __('line.disaster_post', [
                'type' => $data['type'],
                'level' => $data['level'],
                'type_areas' => "• " . implode("\n• ", $data['areas']->toArray()),
            ]) . __('line.close_follow');
            $text = self::flex_box($text);
        } else if ($form == self::Disaster_end) {
            $text = __('line.disaster_end', [
                'type' => $data['type'],
            ]) . __('line.close_follow');
            $text = self::flex_box($text);
        } else if ($form == self::Shelter_post) {
            $text = __('line.shelter_post', ['shelter' => $data['name'], 'location' => $data['gis_map']]) . __('line.close_follow');
            $text = self::flex_box($text);
        } else if ($form == self::Shelter_end) {
            $text = __('line.shelter_end', ['shelter' => $data['name']]) . __('line.close_follow');
            $text = self::flex_box($text);
        } else if ($form == self::Damaged_utility_post) {
            $text = __('line.damaged_utilities_head');
            foreach ($data as $item) {
                $text = $text . __('line.damaged_utilities_detail_post', [
                    'utility' => $item['utility'],
                    'amount' => $item['amount'],
                    'utility_name' => "• " . implode("\n• ", $item['name']),
                ]);
            }
            $text = $text . __('line.close_follow');
            $text = self::flex_box($text);
        } else if ($form == self::Damaged_utility_end) {
            $text = __('line.damaged_utilities_head');
            foreach ($data as $item) {
                $text = $text . __('line.damaged_utilities_detail_end', [
                    'utility' => $item['utility'],
                ]);
            }
            $text = $text . "\n" . __('line.close_follow');
            $text = self::flex_box($text);
        } else if ($form == self::Resource) {
            // $data = [
            //     'product' => 'น้ำ 600 มล.(แพ็ก)',
            //     'shelter' => [
            //         'ศูนย์พักพิงบ้านดอยสะเก็ด',
            //         'ศูนย์พักพิงแม่ฟ้าหลวง',
            //         'ศูนย์พักพิงบ้านห้วยทราย',
            //         'ศูนย์พักพิงดอยหลวงเชียงราย',
            //     ],
            // ];
            $text = __('line.resource', [
                'product' => $data['product'],
                'shelter' => "• " . implode("\n• ", $data['shelter'])
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
                'team' => $data['team'],
                'assigner' => $data['assigner'],
            ]) . __('line.quick');
            $text = self::flex_box_link($text, self::Emc_request_post_team, __('line.see_request'), $type);
        } else if ($form == self::Emc_request_post_team) {
            $text = __('line.emc_request_post_team', [
                'name' => $data['name'],
                'team' => $data['team'],
                'assigner' => $data['assigner'],
            ]) . __('line.quick');
            $text = self::flex_box_link($text, self::Emc_request_post_team, __('line.see_request'), $type);
        } else if ($form == self::Emc_request_end) {
            $text = __('line.emc_request_end', [
                'name' => $data['name'],
                'team' => $data['team'],
                'assigner' => $data['assigner'],
            ]);
            $text = self::flex_box($text);
        } else if ($form == self::Emc_assigned) {
            $text = __('line.emc_assigned', [
                'name' => $data['name'],
                'team' => $data['team'],
                'assigner' => $data['assigner'],
                'receiver' => $data['receiver'],
            ]) . __('line.quick');
            $text = self::flex_box_link($text, self::Emc_assigned, __('line.see_request'), $type);
        } else if ($form == self::Emc_success) {
            $text = __('line.emc_success', [
                'name' => $data['name'],
                'team' => $data['team'],
                'assigner' => $data['assigner'],
                'receiver' => $data['receiver'],
                'status' => $data['status'],
                'note' => $data['note'] ?? '-',
            ]);
            $text = self::flex_box_link($text, self::Emc_success, __('line.see_request'), $type);
        } else if ($form == self::Emc_unsuccess) {
            $text = __('line.emc_success', [
                'name' => $data['name'],
                'team' => $data['team'],
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
        } else if ($form == self::Appointment) {
            // $data = [
            //     'shelter' => 'sh',
            //     'date' => '30 สค 2468',
            //     'time' => 1,
            //     'team' => '55',
            // ];
            $text = __('line.appointment', [
                'shelter' => $data['shelter'],
                'date' => DateUtility::th_time($data['date']),
                'time' => ShiftType::from($data['time'])->label(),
                'team' => $data['team'],
            ]);
            $text = self::flex_box($text);
        } else if ($form == self::Appointment_update) {
            // $data = [
            //     'shelter' => 'xxx x x1',
            //     'old_date' => '2015-03-11',
            //     'old_time' => 1,
            //     'old_teams' => 'xxxx',
            //     'new_date' => '2015-07-15',
            //     'new_time' => 1,
            //     'new_teams' => '5xxxx25',
            // ];
            $text = __('line.appointment_update', [
                'shelter' => $data['shelter'],
                'old_date' => DateUtility::th_time($data['old_date']),
                'old_time' => ShiftType::from($data['old_time'])->label(),
                'old_teams' => $data['old_teams'],
                'new_date' => DateUtility::th_time($data['new_date']),
                'new_time' => ShiftType::from($data['new_time'])->label(),
                'new_teams' => $data['new_teams'],
            ]);
            $text = self::flex_box($text);
        } else if ($form == self::Appointment_canceled) {
            // $data = [
            //     'shelter' => 'xxx x x1',
            //     'date' => '2015-03-11',
            //     'time' => 1,
            //     'teams' => 'xxxx',
            // ];
            $text = __('line.appointment_canceled', [
                'shelter' => $data['shelter'],
                'date' => DateUtility::th_time($data['date']),
                'time' => ShiftType::from($data['time'])->label(),
                'teams' => $data['teams'],
            ]);
            $text = self::flex_box($text);
        } else if ($form == self::Disaster_flood) {
            // $data = [
            //     'water_source' => 'xxx x frx1',
            //     'water_source_type' => '2015-03-11',
            //     'address' => 1,
            //     'water_value' => 999,
            //     'status' => 1,
            // ];
            $text = __('line.disaster_flood', [
                'water_source' => $data['water_source'],
                'water_source_type' => $data['water_source_type'],
                'address' => $data['address'],
                'water_value' => $data['water_value'],
                'status' => FloodStatus::from($data['status'])->label(),
            ]) . __('line.close_follow');
            $text = self::flex_box($text);
        } else if ($form == self::Disaster_landslide) {
            // $data = [
            //     'name' => 'xxx x x1',
            // ];
            $text = __('line.disaster_landslide', [
                'name' => $data['name'],
            ]) . __('line.close_follow');
            $text = self::flex_box_link($text, self::Disaster_landslide, __('line.see_more'), $type);
        } else if ($form == self::Disaster_wildfire_m1) {
            // $data = [
            //     'modis' => 'xx1',
            //     'viirs' => 'xxx2',
            // ];
            $text = __('line.disaster_wildfire_h') . __('line.disaster_wildfire_m1', [
                'modis' => $data['modis'],
                'viirs' => $data['viirs'],
            ]) . __('line.close_follow');
            $text = self::flex_box($text);
        } else if ($form == self::Disaster_wildfire_m2) {
            // $data = [
            //     [
            //         'district' => 'jj',
            //         'modis' => 'xx1',
            //         'viirs' => 'xx2',
            //     ],
            //     [
            //         'district' => 'aa',
            //         'modis' => 'aa1',
            //         'viirs' => 'aa2',
            //     ],

            // ];
            $text = __('line.disaster_wildfire_h')
                . "ข้อมูล " . count($data) . " อันดับ จุดความร้อน\n";

            foreach ($data as $item) {
                $text = $text . __('line.disaster_wildfire_m2', [
                    'district' => "• " . $item['modis'],
                    'modis' => $item['modis'],
                    'viirs' => $item['viirs'],
                ]);
            }
            $text = $text . __('line.close_follow');
            $text = self::flex_box($text);
        } else if ($form == self::Disaster_wildfire_m3) {
            // $data = [
            //     141,
            //     141,
            //     141,
            //     141,
            //     141,
            //     141,
            // ];
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
            // $data = [
            //     141,
            //     141,
            //     141,
            //     141,
            //     141,
            //     141,
            // ];
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
            // $data = [
            //     'source' => 'xxxxxx',
            //     'address' => '123',
            //     'smog_value' => '1',
            //     'status' => '1',
            // ];
            $text = __('line.disaster_smog', [
                'source' => $data['source'],
                'address' => $data['address'],
                'smog_value' => $data['smog_value'],
                'status' => SmogStatus::tryFrom($data['status'])->label(),
            ]) . __('line.close_follow');
            $text = self::flex_box($text);
        } else if ($form == self::Disaster_earthquake) {
            $data = [
                'address' => 'เชียงราย ประเทศไทย',
                'occurrence_time' => '2025-06-06 02:30:00',
                'latitude' => 19.9132130,
                'longitude' => 99.490000,
                'magnitude' => 7.8,
                'depth' => 3.9,
            ];
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
        // $userIds = ['Uaa3e2b17e373580b16101b64937e4a39'];

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

    private function flex_box($text)
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
                ]
            ]
        ];
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
        } else if ($chanel == self::Emc_unsuccess) {
        } else if ($chanel == self::Disaster_landslide) {
        }
    }
}
