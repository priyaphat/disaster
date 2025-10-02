<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\DamagedUtility;
use App\Models\DamagedUtilityLogs;
use App\Services\DamageUtilityService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Enums\DamagedUtility as Enums;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DamageUtilityController extends Controller
{
    use AuthorizesRequests;
    protected $dmgService;

    public function __construct(DamageUtilityService $dmgService)
    {
        $this->dmgService = $dmgService;
    }
    //
    /**
     * สาธารณประโยชน์ที่ได้รับความเสียหาย | show // type name paginate
     */
    public function dmgs(Request $request, $type = null)
    {
        $data = $this->dmgService->damaged_utilities($request->all(), $type);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * สาธารณประโยชน์ที่ได้รับความเสียหาย | ดึง ชื่อ
     */
    public function dmg_name()
    {
        $data = $this->dmgService->utilities();
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * สาธารณประโยชน์ที่ได้รับความเสียหาย | create // type, name
     */
    public function dmg_stroe(Request $request)
    {
        $this->authorize('create', DamagedUtility::class);
        $data = $request->validate([
            'type' => 'required|in:' . implode(',', array_map(fn($e) => $e->value, Enums::cases())),
            'name' => 'required|string|max:255',
            'phone' => 'string|max:10',
            'address' => 'string|max:255',
            'geography_id' => 'exists:geography,id',
        ]);

        if ($data = DamagedUtility::create($data)) {
            self::dul_log($data, DamagedUtilityLogs::created);
            (new LineController)->LineNotification($data->toArray(), LineController::Damaged_utility_post);
            $total = DamagedUtility::where('type', $data->type->value)->count();
            $notification = app(NotificationService::class)->createNotification(
                'damaged.updated',
                [
                    'name' => $data->type->label(),
                    'total' => $total
                ],
                $data->id,
                DamagedUtility::class,
                true
            );
            app(NotificationService::class)->sendToAllUsers($notification);

            return ApiResponse::successResponse(__('success.damaged_utility.created'), $data);
        }
    }

    /**
     * สาธารณประโยชน์ที่ได้รับความเสียหาย | updated // type, name
     */
    public function dmg_update(Request $request, DamagedUtility $id)
    {
        $this->authorize('update', DamagedUtility::class);
        $data = $request->validate([
            'type' => 'required|in:' . implode(',', array_map(fn($e) => $e->value, Enums::cases())),
            'name' => 'required|string|max:255',
            'phone' => 'string|max:10',
            'address' => 'string|max:255',
            'geography_id' => 'exists:geography,id',
        ], []);

        if ($id->update($data)) {
            self::dul_log($id, DamagedUtilityLogs::updated);
            (new LineController)->LineNotification($id->toArray(), LineController::Damaged_utility_post);
            $total = DamagedUtility::where('type', $id->type->value)->count();
            $notification = app(NotificationService::class)->createNotification(
                'damaged.updated',
                [
                    'name' => $id->type->label(),
                    'total' => $total
                ],
                $id->id,
                DamagedUtility::class,
                true
            );
            app(NotificationService::class)->sendToAllUsers($notification);
            return ApiResponse::successResponse(__('success.damaged_utility.created'), $this->dmgService->damaged_utility($id->id));
        }
    }

    /**
     * สาธารณประโยชน์ที่ได้รับความเสียหาย | dalete // type
     */
    public function dmg_delete(DamagedUtility $damaged)
    {
        $this->authorize('delete', DamagedUtility::class);
        $damaged->delete();
        self::dul_log($damaged, DamagedUtilityLogs::deleted);
        (new LineController)->LineNotification($damaged->toArray(), LineController::Damaged_utility_end);
        $total = DamagedUtility::where('type', $damaged->type->value)->count();
        $notification = app(NotificationService::class)->createNotification(
            'damaged.updated',
            [
                'name' => $damaged->type->label(),
                'total' => $total
            ],
            $damaged->id,
            DamagedUtility::class,
            true
        );
        app(NotificationService::class)->sendToAllUsers($notification);
        return ApiResponse::successResponse(__('success.damaged_utility.deleted'));
    }

    /**
     * สาธารณประโยชน์ที่ได้รับความเสียหาย log
     */
    public function damaged_utility_logs(Request $r)
    {
        $q = DamagedUtilityLogs::select(
            'damaged_utility_logs.*',
            DB::raw('concat(users.firstname," ",users.lastname) as created_name')
        )
            ->leftJoin('users', 'damaged_utility_logs.created_by', 'users.id')
            ->when(
                $r->search,
                fn($q) =>
                $q->where(DB::raw('concat(users.firstname," ",users.lastname)'), 'like', '%' . $r->search . '%')
            )
            ->when($r->start_date && $r->end_date, fn($q) =>
            $q->whereBetween('damaged_utility_logs.created_at', [
                Carbon::parse($r->start_date)->startOfDay(),
                Carbon::parse($r->end_date)->endOfDay()
            ]))
            ->when(
                $r->type,
                fn($q) =>
                $q->where('damaged_utility_logs.type', $r->type)
            )
            ->when(
                $r->status,
                fn($q) =>
                $q->where('damaged_utility_logs.status', $r->status)
            )->paginate($r->paginate ?? 10);
        return ApiResponse::successResponse(__('success.data'), $q);
    }

    private function dul_log($data, $status)
    {
        $q = new DamagedUtilityLogs();
        if ($status == DamagedUtilityLogs::created) $data['datail'] = 'เพิ่มสถานที่ ' . $data['name'];
        if ($status == DamagedUtilityLogs::updated) $data['datail'] = 'แก้ไขสถานที่ ' . $data['name'];
        if ($status == DamagedUtilityLogs::deleted) $data['datail'] = 'ลบสถานที่ ' . $data['name'];
        $q['datail'] = $data;
        $q['type'] = $data['type'];
        $q['status'] = $status;
        $q->save();
    }
}
