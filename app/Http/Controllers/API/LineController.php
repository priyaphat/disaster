<?php

namespace App\Http\Controllers\API;

use App\Enums\ShiftType;
use App\Http\Controllers\Controller;
use App\Http\Utility\DateUtility;
use App\Models\LineUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Config;

class LineController extends Controller
{

    public const Population = 1;
    public const User = 7;

    public const Disaster_post = 1; //แจ้งเตือน ยุติเหตุภัยพิบัติ
    public const Disaster_end = 2; //
    public const Shelter_post = 3; //แจ้งเตือน เปิดทำการศูนย์พักพิง
    public const Shelter_end = 4; // แจ้งเตือน ปิดทำการศูนย์พักพิง
    public const Damaged_utility_post = 5; //แจ้งเตือน อัปเดตสาธารณประโยชน์ที่ได้รับความเสียหาย
    public const Damaged_utility_end = 6; //
    public const Resoure = 7;  //แจ้งเตือน คำร้องขอทรัพยากร
    public const News_post = 8; //แจ้งเตือน ยุติเหตุภัยพิบัติ
    public const News_end = 9; //
    public const Emc_request_post = 10; //แจ้งเตือน คำร้องขอใหม่ req id
    public const Emc_request_post_team = 11; //แจ้งเตือน รายการรอช่วยเหลือใหม่ (ส่งให้หัวหน้าทีมที่เกี่ยวข้องอนุมัติ) req id
    public const Emc_request_end = 12; // แจ้งเตือน รายการรอช่วยเหลือถูกยกเลิก (ส่งให้หัวหน้าทีมที่เกี่ยวข้อง) req id
    public const Emc_assigned = 13; // แจ้งเตือน รายการรอช่วยเหลือใหม่ (ส่งให้สมาชิกในทีมที่เกี่ยวข้องเมื่ออนุมัติสำเร็จ) req id
    public const User_register = 15; //แจ้งเตือน คำร้องขอลงทะเบียนใหม่ req id
    public const User_on = 16; //แจ้งเตือน เจ้าหน้าที่ถูก ย้าย / นำเข้า / นำออก ศูนย์พักพิง req id
    public const User_off = 17; //
    public const User_join = 18; //แจ้งเตือน เจ้าหน้าที่ถูก ย้าย / นำเข้า / นำออก ทีมเจ้าหน้าที่ req id
    public const User_leave = 19; //
    public const Appointment = 20; //แจ้งเตือน นัดหมายตรวจเยี่ยมศูนย์พักพิงใหม่ req id
    public const Appointment_update = 21; //แจ้งเตือน แก้ไขนัดหมายตรวจเยี่ยมศูนย์พักพิง req id
    public const Appointment_canceled = 22; //แจ้งเตือน นัดหมายตรวจเยี่ยมศูนย์พักพิงถูกยกเลิก req id
    public const Emc_success = 14; //แจ้งเตือน รายงานผลการช่วยเหลือ (ส่งให้เจ้าหน้าที่ส่วนกลาง (ทุกสถานะ)) req id
    public const Emc_unsuccess = 23; //
    public const Disaster_flood = 24;
    public const Disaster_landslide = 25;
    public const Disaster_wildfire_m1 = 26.1;
    public const Disaster_wildfire_m2 = 26.2;
    public const Disaster_wildfire_m3 = 26.3;
    public const Disaster_wildfire_m4 = 26.4;
    public const Disaster_smog = 27;
    public const Disaster_earthquake = 28;

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

    public function test()
    {


        $data = [
            'product' => 'น้ำ 600 มล.(แพ็ก)',
            'shelter' => [
                'ศูนย์พักพิงบ้านดอยสะเก็ด',
                'ศูนย์พักพิงแม่ฟ้าหลวง',
                'ศูนย์พักพิงบ้านห้วยทราย',
                'ศูนย์พักพิงดอยหลวงเชียงราย',
            ],
        ];
        // $recipint = ['Uaa3e2b17e373580b16101b64937e4a39'];

        return self::LineNotification($data, self::Disaster_post);
    }

    public function LineNotification(array $data = null, $form, $recipint = null)
    {
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

    private function form_box($form, $data = null, $type = null)
    {
        if ($form == self::Disaster_post) {
            // $data = [
            //     'type' => 'ดินถล่ม',
            //     'level' => 3,
            //     'areas' => [
            //         'เมืองเชียงราย',
            //         'อำเภอแม่จัน',
            //         'อำเภอแม่ฟ้าหลวง',
            //         'อำเภอแม่สาย',
            //     ],
            // ];
            $text = __('line.disaster_post', [
                'type' => $data['type'],
                'level' => $data['level'],
                'type_areas' => "• " . implode("\n• ", $data['areas']),
            ]) . __('line.close_follow');
            $text = self::flex_box($text);
        } else if ($form == self::Disaster_end) {
            // $data = [
            //     'type'=>'ดินถล่ม'
            // ];
            $text = __('line.disaster_end', [
                'type' => $data['type'],
            ]) . __('line.close_follow');
            $text = self::flex_box($text);
        } else if ($form == self::Shelter_post) {
            // $data = [
            //     'shelter' => 'xxxx',
            //     'location' => 'x.xxx,x.xxx'
            // ];
            $text = __('line.shelter_post', ['shelter' => $data['shelter'], 'location' => $data['location']]) . __('line.close_follow');
            $text = self::flex_box($text);
        } else if ($form == self::Shelter_end) {
            // $data = [
            //     'shelter' => 'xxxx',
            // ];
            $text = __('line.shelter_end', ['shelter' => $data['shelter']]) . __('line.close_follow');
            $text = self::flex_box($text);
        } else if ($form == self::Damaged_utility_post) {
            // $data = [
            //     [
            //         'utility' => 'xxx',
            //         'amount' => 3,
            //         'name' => [
            //             'xxx1',
            //             'xxx2',
            //             'xxx3',
            //         ]
            //     ],
            //     [
            //         'utility' => 'xxx2',
            //         'amount' => 1,
            //         'name' => [
            //             'xxx1',
            //         ]
            //     ]
            // ];
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
            // $data = [
            //     [
            //         'utility' => 'xxx',
            //     ],
            //     [
            //         'utility' => 'xxx2',
            //     ]
            // ];
            $text = __('line.damaged_utilities_head');
            foreach ($data as $item) {
                $text = $text . __('line.damaged_utilities_detail_end', [
                    'utility' => $item['utility'],
                ]);
            }
            $text = $text . "\n" . __('line.close_follow');
            $text = self::flex_box($text);
        } else if ($form == self::Resoure) {
            // $data = [
            //     'product' => 'น้ำ 600 มล.(แพ็ก)',
            //     'shelter' => [
            //         'ศูนย์พักพิงบ้านดอยสะเก็ด',
            //         'ศูนย์พักพิงแม่ฟ้าหลวง',
            //         'ศูนย์พักพิงบ้านห้วยทราย',
            //         'ศูนย์พักพิงดอยหลวงเชียงราย',
            //     ],
            // ];
            $text = __('line.resoure', [
                'product' => $data['product'],
                'shelter' => "• " . implode("\n• ", $data['shelter'])
            ]) . __('line.close_follow');
            $text = self::flex_box_link($text, self::Resoure, __('line.see_more'), $type);
        } else if ($form == self::News_post) {
            // $data = [
            //     'name' => 'xxxxxxxx',
            // ];
            $text = __('line.news_post', [
                'name' => $data['name'],
            ]);
            $text = self::flex_box_link($text, self::News_post, __('line.read_more'), $type);
        } else if ($form == self::News_end) {
            // $data = [
            //     'name' => 'xxxxxxxx',
            // ];
            $text = __('line.news_end', [
                'name' => $data['name'],
            ]);
            $text = self::flex_box_link($text, self::News_end, __('line.read_more'), $type);
        } else if ($form == self::Emc_request_post) {
            // $data = [
            //     'name' => 'xxxx xxxx',
            // ];
            $text = __('line.emc_request_post', [
                'name' => $data['name'],
            ]) . __('line.quick');
            $text = self::flex_box_link($text, self::Emc_request_post, __('line.see_request'), $type);
        } else if ($form == self::Emc_request_post_team) {
            // $data = [
            //     'name' => 'xxxx xxxx',
            //     'team' => 'xxxx2',
            //     'assigner' => 'xxx xxx',
            // ];
            $text = __('line.emc_request_post_team', [
                'name' => $data['name'],
                'team' => $data['team'],
                'assigner' => $data['assigner'],
            ]) . __('line.quick');
            $text = self::flex_box_link($text, self::Emc_request_post_team, __('line.see_request'), $type);
        } else if ($form == self::Emc_request_end) {
            // $data = [
            //     'name' => 'xxxx xxxx',
            //     'team' => 'xxxx2',
            //     'assigner' => 'xxx xxx',
            // ];
            $text = __('line.emc_request_end', [
                'name' => $data['name'],
                'team' => $data['team'],
                'assigner' => $data['assigner'],
            ]);
            $text = self::flex_box($text);
        } else if ($form == self::Emc_assigned) {
            // $data = [
            //     'name' => 'xxxx xxxx',
            //     'team' => 'xxxx2',
            //     'assigner' => 'xxx xxx',
            //     'receiver' => 'xxxxxx xxxxx',
            // ];
            $text = __('line.emc_assigned', [
                'name' => $data['name'],
                'team' => $data['team'],
                'assigner' => $data['assigner'],
                'receiver' => $data['receiver'],
            ]) . __('line.quick');
            $text = self::flex_box_link($text, self::Emc_assigned, __('line.see_request'), $type);
        } else if ($form == self::Emc_success) {
            // $data = [
            //     'name' => 'xxxx xxxx',
            //     'team' => 'xxxx2',
            //     'assigner' => 'xxx xxx',
            //     'receiver' => 'xxxxxx xxxxx',
            //     'status' => 'xxx',
            //     'note' => 'xxxxxx',
            // ];
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
            // $data = [
            //     'name' => 'xxxx xxxx',
            //     'team' => 'xxxx2',
            //     'assigner' => 'xxx xxx',
            //     'receiver' => 'xxxxxx xxxxx',
            //     'status' => 'xxx',
            //     'note' => 'xxxxxx',
            // ];
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
            // $data = [
            //     'shelter' => 'xxxxxxx'
            // ];
            $text = __('line.user_on', [
                'shelter' => $data['shelter'],
            ]);
            $text = self::flex_box($text);
        } else if ($form == self::User_off) {
            // $data = [
            //     'shelter' => 'xxxxxxx'
            // ];
            $text = __('line.user_off', [
                'shelter' => $data['shelter'],
            ]);
            $text = self::flex_box($text);
        } else if ($form == self::User_join) {
            // $data = [
            //     'team' => 'xxxxxxx 2'
            // ];
            $text = __('line.user_join', [
                'team' => $data['team'],
            ]);
            $text = self::flex_box($text);
        } else if ($form == self::User_leave) {
            // $data = [
            //     'team' => 'xxxxxxx 2'
            // ];
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
        } else if($form== self::Disaster_flood){
            
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

        if ($chanel == self::Resoure) {
            if ($type == self::User) return redirect('/dashboard');
            return redirect('/home-page');
        } else if ($chanel == self::News_post || $chanel == self::News_end) {
            return redirect('/home-page');
        } else if ($chanel == self::Emc_request_post) {
            return redirect('/emergency-requests/requests');
        } else if ($chanel == self::Emc_request_post_team || $chanel == self::Emc_assigned) {
            return redirect('/emergency-requests/waiting-lists-for-help');
        } else if ($chanel == self::Emc_success) {
        } else if ($chanel == self::Emc_unsuccess) {
        } else if ($chanel == self::Disaster_landslide){

        }
    }
}
