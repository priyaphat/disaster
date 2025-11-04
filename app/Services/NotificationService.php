<?php

namespace App\Services;

use App\Enums\TeamPosition;
use App\Http\Resources\NotificationResource;
use App\Jobs\BroadcastNotification;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NotificationService
{
    public function getList(array $filters = [])
    {
        $offset = $filters['offset'] ?? 0;
        $limit = $filters['limit'] ?? 100;

        $query = Notification::with(['notification_users' => function ($q) {
            $q->where('user_id', Auth::user()->id);
        }])->whereHas('notification_users', function ($q) {
            $q->where('user_id', Auth::user()->id);
        })->skip($offset)->take($limit)->latest('created_at');

        return NotificationResource::collection($query->get());
    }

    public function markAllAsRead()
    {
        $data = NotificationUser::where(['user_id' => Auth::user()->id, 'is_read' => false])->update(['is_read' => true, 'readed_at' => Carbon::now()]);
        return $data;
    }

    public function bulkDelte(array $data)
    {
        $data = NotificationUser::whereIn('notification_id', $data['notification_id'])->where('user_id', Auth::user()->id)->delete();
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
        [$eventType, $eventAction] = explode('.', $type);
        $notification = self::formatData($type, $detail);
        $data = [
            'notification' => $notification,
            'entity_id' => $entityId,
            'entity_type' => $entityType,
            'notification_type' => $type,
            'event_type' => $eventType,
            'event_action' => $eventAction,
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
            if ($key === 'unit') {
                $value = $value ? Str::of('(')->append($value)->append(')')->toString() : '';
            } else if ($key === 'amount' || $key === 'total') {
                $value = number_format($value);
            }
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

    public function sendToAdmin($notification, $chunkSize = 100)
    {
        User::whereHas('roles', function ($q) {
            $q->where('name', Role::ADMIN);
        })->chunk($chunkSize, function ($users) use ($notification) {
            BroadcastNotification::dispatch($users, $notification, Role::ADMIN);
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
            if (is_array($teamId)) {
                $query->whereIn('team_id', $teamId);
            } else {
                $query->where('team_id', $teamId);
            }
        })->chunk($chunkSize, function ($users) use ($notification) {
            BroadcastNotification::dispatch($users, $notification);
        });
    }

    public function sendToTeamLeader($notification, $teamId, $chunkSize = 100)
    {
        User::whereHas('member', function ($query) use ($teamId) {
            if (is_array($teamId)) {
                $query->whereIn('team_id', $teamId);
            } else {
                $query->where('team_id', $teamId);
            }
            $query->where('team_position', TeamPosition::LEADER);
        })->chunk($chunkSize, function ($users) use ($notification) {
            BroadcastNotification::dispatch($users, $notification);
        });
    }

    public function sendToTeamMember($notification, $teamId, $chunkSize = 100)
    {
        User::whereHas('member', function ($query) use ($teamId) {
            if (is_array($teamId)) {
                $query->whereIn('team_id', $teamId);
            } else {
                $query->where('team_id', $teamId);
            }
            $query->where('team_position', TeamPosition::MEMBER);
        })->chunk($chunkSize, function ($users) use ($notification) {
            BroadcastNotification::dispatch($users, $notification);
        });
    }
}
