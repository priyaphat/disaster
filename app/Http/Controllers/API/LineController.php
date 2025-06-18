<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LineController extends Controller
{

    private const OneId = 1;
    private const All = 2;
    private const SelectId = 3;

    public const POST = 1;

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

    public function LineNotification($recipient = null, $form = null, array $data = null, $broadcast  = null)
    {
        $data = [
            'type' => 'ดินถล่ม',
            'level' => 3,
            'areas' => [
                'เมืองเชียงราย',
                'อำเภอแม่จัน',
                'อำเภอแม่ฟ้าหลวง',
                'อำเภอแม่สาย',
            ],
        ];
        $text = self::form_box(self::POST, $data);
        // $message = self::flex_box($text);
        return $text;
        // if ($broadcast == self::OneId) self::broadcast_push($recipient, $message);
        // if ($broadcast == self::All) self::broadcast_all($message);
        // if ($broadcast == self::SelectId) self::broadcast_push($recipient, $message);
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
            'messages' => [
                [
                    'type' => 'text',
                    'text' => '📢 ประกาศ: ระบบจะปิดปรับปรุงคืนนี้!',
                ]
            ],
        ]);
    }

    private function broadcast_multi(array $userId, $message) // array userId
    {
        $userIds = ['U123...', 'U456...', 'U789...'];

        Http::withToken(env('LINE_BOT_CHANNEL_ACCESS_TOKEN'))->post('https://api.line.me/v2/bot/message/multicast', [
            'to' => $userIds,
            'messages' => [
                [
                    'type' => 'text',
                    'text' => '🎯 แจ้งเฉพาะกลุ่มสมาชิกที่เลือกไว้',
                ]
            ],
        ]);
    }

    private function flex_box_link($text)
    {
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
        return $message;
    }

    private function flex_box($text)
    {
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
                            'text' => $text,
                            'wrap' => true
                        ]
                    ]
                ]
            ]
        ];
        return $message;
    }

    private function form_box($form, $data)
    {
        if ($form == self::POST) {
            $text = __('line.disaster_post', [
                'type' => $data['type'],
                'level' => $data['level'],
                'type_areas' => implode("\n", $data['areas']),
            ]).__('line.close_follow');
        }

        return $text;
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
}
