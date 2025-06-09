<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Log;

class LineController extends Controller
{
    public function handle(Request $request)
    {
        $events = $request->input('events', []);

        foreach ($events as $event) {
            if ($event['type'] === 'message') {
                $replyToken = $event['replyToken'];
                $text = $event['message']['text'];

                $this->replyText($replyToken, "คุณพิมพ์ว่า: {$text}");
            }
        }

        return response()->json(['status' => 'ok']);
    }

    private function replyText($replyToken, $text)
    {
        $url = 'https://api.line.me/v2/bot/message/reply';

        Http::withToken(env('LINE_BOT_CHANNEL_ACCESS_TOKEN'))
            ->post($url, [
                'replyToken' => $replyToken,
                'messages' => [
                    ['type' => 'text', 'text' => $text]
                ],
            ]);
    }
}
