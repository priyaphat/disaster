<?php

namespace App\Services;

use App\Enums\TeamPosition;
use App\Http\Resources\NotificationResource;
use App\Jobs\BroadcastNotification;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\User;
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

    public function markAllAsRead()
    {
        $data = NotificationUser::where('user_id', Auth::user()->id)->update(['is_read' => true]);
        return $data;
    }

    public function bulkDelte(array $notiArr)
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
            'notification_type' => $type,
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
            $description .= $item['content'] . ' ';
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

    public function sendToSelfUser($notification, $userId)
    {
        $user = User::where('id', $userId)->get();
        BroadcastNotification::dispatch($user, $notification);
    }

    public function sendToAllUsers($notification, $chunkSize = 100)
    {
        $data['approve'] = 'approved';
        User::filter($data)
            ->chunk($chunkSize, function ($users) use ($notification) {
                BroadcastNotification::dispatch($users, $notification);
            });
    }

    public function sendToCenterStaff($notification, $chunkSize = 100)
    {
        User::where('username', 'superadmin')
            ->chunk($chunkSize, function ($users) use ($notification) {
                BroadcastNotification::dispatch($users, $notification);
            });
    }

    public function sendToStaff($notification, $shelterId, $chunkSize = 100)
    {
        User::whereHas('staff', function ($query) use ($shelterId) {
            $query->where('shelter_id', $shelterId);
        })->chunk($chunkSize, function ($users) use ($notification) {
            BroadcastNotification::dispatch($users, $notification);
        });
    }

    public function sendToTeam($notification, $teamId, $chunkSize = 100)
    {
        User::whereHas('member', function ($query) use ($teamId) {
            $query->where('team_id', $teamId);
        })->chunk($chunkSize, function ($users) use ($notification) {
            BroadcastNotification::dispatch($users, $notification);
        });
    }

    public function sendToTeamLeader($notification, $teamId, $chunkSize = 100)
    {
        User::whereHas('member', function ($query) use ($teamId) {
            $query->where('team_id', $teamId)
                ->where('team_position', TeamPosition::LEADER);
        })->chunk($chunkSize, function ($users) use ($notification) {
            BroadcastNotification::dispatch($users, $notification);
        });
    }

    public function sendToTeamMember($notification, $teamId, $chunkSize = 100)
    {
        User::whereHas('member', function ($query) use ($teamId) {
            $query->where('team_id', $teamId)
                ->where('team_position', TeamPosition::MEMBER);
        })->chunk($chunkSize, function ($users) use ($notification) {
            BroadcastNotification::dispatch($users, $notification);
        });
    }


}
