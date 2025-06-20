<?php

namespace App\Http\Controllers\API;

use App\Enums\ShiftType;
use App\Http\Controllers\Controller;
use App\Http\Utility\DateUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LineController extends Controller
{

    // private const OneId = 1;
    private const All = 2;
    private const SelectId = 3;

    public const Disaster_post = 1;
    public const Disaster_end = 2;
    public const Shelter_post = 3;
    public const Shelter_end = 4;
    public const Damaged_utility_post = 5;
    public const Damaged_utility_end = 6;
    public const Resoure = 7;
    public const News_post = 8;
    public const News_end = 9;
    public const Emc_request_post = 10;
    public const Emc_request_post_team = 11;
    public const Emc_request_end = 12;
    public const Emc_assigned = 13;
    public const Emc_success = 14;
    public const User_register = 15;
    public const User_on = 16;
    public const User_off = 17;
    public const User_join = 18;
    public const User_leave = 19;
    public const Appointment = 20;
    public const Appointment_update = 21;
    public const Appointment_canceled = 22;

    public function handle(Request $request)
    {
        if ($request->events) {
            $event = $request->events[0];
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
            }
        }
        return response()->json(['status' => 'ok']);
    }

    public function LineNotification($recipient = null, $form = null, array $data = null, $broadcast  = self::All)
    {
        $text = self::form_box(self::User_register, $data);
        // $message = self::flex_box($text);'
        $message = self::flex_box_link($text, self::User_register);
        // self::broadcast_multi(null, $message);
        // return $message;
        if ($broadcast == self::All) self::broadcast_all($message);
        // if ($broadcast == self::SelectId) self::broadcast_push($recipient, $message);
        return 'ok';
    }

    private function form_box($form, $data = null)
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
        } else if ($form == self::Disaster_end) {
            // $data = [
            //     'type'=>'ดินถล่ม'
            // ];
            $text = __('line.disaster_end') . __('line.close_follow');
        } else if ($form == self::Shelter_post) {
            // $data = [
            //     'shelter' => 'xxxx',
            //     'location' => 'x.xxx,x.xxx'
            // ];
            $text = __('line.shelter_post', ['shelter' => $data['shelter'], 'location' => $data['location']]) . __('line.close_follow');
        } else if ($form == self::Shelter_end) {
            // $data = [
            //     'shelter' => 'xxxx',
            // ];
            $text = __('line.shelter_end', ['shelter' => $data['shelter']]) . __('line.close_follow');
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
        } else if ($form == self::News_post) {
            // $data = [
            //     'name' => 'xxxxxxxx',
            // ];
            $text = __('line.news_post', [
                'name' => $data['name'],
            ]);
        } else if ($form == self::News_end) {
            // $data = [
            //     'name' => 'xxxxxxxx',
            // ];
            $text = __('line.news_end', [
                'name' => $data['name'],
            ]);
        } else if ($form == self::Emc_request_post) {
            // $data = [
            //     'name' => 'xxxx xxxx',
            // ];
            $text = __('line.emc_request_post', [
                'name' => $data['name'],
            ]) . __('line.quick');
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
        } else if ($form == self::Emc_success) // and unsuccess
        {
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
        } else if ($form == self::User_register) {
            $text = __('line.user_register');
        } else if ($form == self::User_on) {
            // $data = [
            //     'shelter' => 'xxxxxxx'
            // ];
            $text = __('line.user_on', [
                'shelter' => $data['shelter'],
            ]);
        } else if ($form == self::User_off) {
            // $data = [
            //     'shelter' => 'xxxxxxx'
            // ];
            $text = __('line.user_off', [
                'shelter' => $data['shelter'],
            ]);
        } else if ($form == self::User_join) {
            // $data = [
            //     'team' => 'xxxxxxx 2'
            // ];
            $text = __('line.user_join', [
                'team' => $data['team'],
            ]);
        } else if ($form == self::User_leave) {
            // $data = [
            //     'team' => 'xxxxxxx 2'
            // ];
            $text = __('line.user_leave', [
                'team' => $data['team'],
            ]);
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

    private function broadcast_all($message) // to all 
    {
        Http::withToken(env('LINE_BOT_CHANNEL_ACCESS_TOKEN'))->post('https://api.line.me/v2/bot/message/broadcast', [
            'messages' => [$message],
        ]);
    }

    private function broadcast_multi(array $userId = null, $message) // array userId
    {
        $userIds = ['Uaa3e2b17e373580b16101b64937e4a39'];

        Http::withToken(env('LINE_BOT_CHANNEL_ACCESS_TOKEN'))->post('https://api.line.me/v2/bot/message/multicast', [
            'to' => $userIds,
            'messages' => [$message],
        ]);
    }

    private function flex_box_link($text, $link)
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
                                'label' => 'อ่านข่าวประชาสัมพันธ์',
                                'uri' => env('APP_URL') . '/api/line-param/' . $link,
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
            'altText' => 'ข้อความแจ้งเตือนใหม่',
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

    public function chanel($chanel)
    {
        return view('floods');
    }
}
