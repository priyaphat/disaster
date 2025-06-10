<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Log;

class LineController extends Controller
{
    private $liff_login = 'https://liff.line.me/2007547242-Rmgq4X9x';

    public function handle(Request $request)
    {
        log::info($request);
        $event = $request->events[0];

        if ($event['type'] === 'message') {
            $replyToken = $event['replyToken'];
            $text = $event['message']['text'];
            if ($text === 'h') {
                $this->rich_change($event['source']['userId'], env('RICHMENU_NO_AUTH'));
            }
            if ($text === 'login') {
                log::info('send login');
                $message = [
                    'type' => 'template',
                    'altText' => 'กรุณาเข้าสู่ระบบ',
                    'template' => [
                        'type' => 'buttons',
                        'title' => 'เข้าสู่ระบบ',
                        'text' => 'กรุณากดปุ่มด้านล่างเพื่อเข้าสู่ระบบ',
                        'actions' => [
                            [
                                'type' => 'uri',
                                'label' => '🔐 Login',
                                'uri' => $this->liff_login,
                            ]
                        ]
                    ]
                ];

                $this->replyMessage($replyToken, [$message]);
            }

            $msg = [
                'replyToken' => $replyToken,
                'messages' => [
                    ['type' => 'text', 'text' => "คุณพิมพ์ว่า: {$text}"]
                ],
            ];
            $this->replyText($replyToken, $msg);
        }


        return response()->json(['status' => 'ok']);
    }

    function replyMessage($replyToken, $messages)
    {
        $httpClient = new \GuzzleHttp\Client();

        $response = $httpClient->post('https://api.line.me/v2/bot/message/reply', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . env('LINE_BOT_CHANNEL_ACCESS_TOKEN'),
            ],
            'json' => [
                'replyToken' => $replyToken,
                'messages' => $messages,
            ],
        ]);

        return $response;
    }


    private function replyText($replyToken, $text)
    {
        $url = 'https://api.line.me/v2/bot/message/reply';

        Http::withToken(env('LINE_BOT_CHANNEL_ACCESS_TOKEN'))
            ->post($url, $text);
    }

    private function rich_change($userId, $richId)
    {
        Http::withToken(env('LINE_BOT_CHANNEL_ACCESS_TOKEN'))
            ->post('https://api.line.me/v2/bot/user/' . $userId . '/richmenu/' . $richId);
    }
}
