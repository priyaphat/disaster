<?php

namespace App\Services;

use App\Http\Resources\NotificationResource;
use App\Jobs\BroadcastNotification;
use App\Models\Notification;
use App\Models\NotificationUser;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    public function getList(array $filters = [], bool $paginate = true, int $perPage = 10)
    {
        $query = NotificationUser::with('notification')->where('user_id', Auth::user()->id);
        if (!$paginate) {
            return NotificationResource::collection($query->get());
        }

        $data = $query->paginate($perPage);
        $paginate = $data->setCollection(NotificationResource::collection($data->items())->collection);
        return  $paginate;
    }

    public function readAll()
    {
        $data = NotificationUser::where('user_id', Auth::user()->id)->update(['is_read' => true]);
        return $data;
    }

    public function delete(array $notiArr)
    {
        $data = NotificationUser::whereIn('id', $notiArr)->delete();
        return $data;
    }

    public function deleteAll()
    {
        $data = NotificationUser::where('user_id', Auth::user()->id)->delete();
        return $data;
    }

    /**
     * Create a notification.
     */
    public function createNotification(string $type, array $detail,  $entityId = null, $entityType = null, $isShow = false)
    {
        $notification = self::formatData($type, $detail);
        $data = [
            'notification' => $notification,
            'entity_id' => $entityId,
            'entity_type' => $entityType,
            'show_details' => $isShow
        ];
        return Notification::create($data);
    }

    public function getFormat(string $type): array
    {
        $format = config('notification.' . $type);
        if (!$format) {
            return config('notification.default');
        }

        return $format;
    }

    /**
     * Format notification data for Vue frontend
     */
    public function formatData(string $type, array $data)
    {
        $format = $this->getFormat($type);
        $description = null;
        $collection = collect($format['message'])->map(function ($item) use ($data, &$description) {
            $item['content'] = self::replacePlaceholders($item['content'], $data);
            $description .= $item['content'];
            return $item;
        });
        return [
            'title' => $format['title'],
            'description' => $description,
            'format' => $collection
        ];
    }

    public function replacePlaceholders(string $text, array $replacements)
    {
        foreach ($replacements as $key => $value) {
            $text = str_replace('#{' . $key . '}', $value, $text);
        }
        return $text;
    }

    public function sendNotification($notification, $userIds)
    {
        // dispatch(new BroadcastNotification($notification, $userIds))->onQueue('notifications');
    }
}
