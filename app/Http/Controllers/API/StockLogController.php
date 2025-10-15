<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Http\Utility\ProductUtility;
use App\Models\Shelter;
use App\Models\ShelterProduct;
use App\Models\StockLogs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\MultiSheetExport;
use App\Exports\StockExport;
use App\Http\Utility\DateUtility;
use Maatwebsite\Excel\Facades\Excel;

class StockLogController extends Controller
{
    public function index(Request $r, $id = null)
    {
        $shelter_id = $r['shelter_id'] ?? ProductUtility::getShelter();
        $q = StockLogs::select(
            'stock_logs.*',
            'p.name',
            DB::raw('concat(u.firstname," ",u.lastname) as created_name'),
            'p.group_id'
        )
            ->leftJoin('products as p', 'p.id', 'stock_logs.product_id')
            ->leftJoin('users as u', 'u.id', 'stock_logs.created_by')
            ->where('stock_logs.shelter_id', $shelter_id)
            ->when(
                $r['group_id'],
                fn($q) =>
                $q->where('p.group_id', $r['group_id'])
            )
            ->when(
                $id,
                fn($q) =>
                $q->where('stock_logs.id', $id)
            )
            ->when(
                $r['search'],
                fn($q) =>
                $q->whereAny([
                    'p.name',
                    DB::raw("concat(u.firstname,' ',u.lastname)")
                ], 'like', "%{$r['search']}%")
            )
            ->when($r['start_date'] && $r['end_date'], fn($q) =>
            $q->whereBetween('stock_logs.created_at', [
                Carbon::parse($r['start_date'])->startOfDay(),
                Carbon::parse($r['end_date'])->endOfDay()
            ]))
            ->orderBy('stock_logs.created_at', $r['sort'] ?? 'desc');

        if ($id) return ApiResponse::successResponse(__('success.data'), $q->first());
        if ($r['paginate']) return ApiResponse::successResponse(__('success.data'), $q->paginate($r['paginate']));
        if ($r['get']) return $q->get();
        return ApiResponse::successResponse(__('success.data'), $q->get());
    }

    public function store($base, $item, $type, $pdt = null, $new_pdt = null)
    {
        if ($type == 1 || $type == 2) {
            $data = [
                'product_id' => $base['product_id'],
                'detail' => $item['detail'],
                'amount' => $item['quantity'],
                'total' => $base['quantity'],
                'type' => $type,
                'shelter_id' => $base['shelter_id'],
            ];
            StockLogs::create($data);
        } else {
            $from =  Shelter::find($pdt['shelter_id'])->name ?? 'ศูนย์กลาง';
            $to =  Shelter::find($base['shelter_id'])->name ?? 'ศูนย์กลาง';
            $detail = [
                'from' => $pdt['shelter_id'],
                'form_name' => $from,
                'to' => $base['shelter_id'],
                'to_name' => $to,
                'reason' => $item['reason'] ?? null,
            ];

            $out_transfer = [
                'product_id' => $item['product_id'],
                'detail' => $detail,
                'amount' => $item['quantity'],
                'total' => $pdt['quantity'],
                'type' => StockLogs::TRANSFEROUT,
                'shelter_id' => $pdt['shelter_id'],
            ];

            StockLogs::create($out_transfer);

            $in_transfer = [
                'product_id' => $item['product_id'],
                'detail' => $detail,
                'amount' => $item['quantity'],
                'total' => $new_pdt['quantity'] ?? ShelterProduct::where('product_id', $item['product_id'])->where('shelter_id', $base['shelter_id'])->value('quantity'),
                'type' => StockLogs::TRANSFERIN,
                'shelter_id' => $base['shelter_id'],
            ];

            StockLogs::create($in_transfer);
        }
    }

    public function exp(Request $r)
    {

        $group = app(ProductController::class)->typeName(true);
        $data = [];
        $shelter = $r['shelter_id'] ?? 0;
        // $shelter = 1;
        foreach ($group as $item) {
            $req = new Request();
            $req['shelter_id'] = $shelter;
            $req['get'] = true;
            if ($item['id']) $req['group_id'] = $item['id'];
            if ($r['start_date'] && $r['end_date']) {
                $req['start_date'] = $r['start_date'];
                $req['end_date'] = $r['end_date'];
            }
            if ($r['search']) $req['search'] = $r['search'];
            if ($r['sort']) $req['sort'] = $r['sort'];
            $q = self::index($req);

            $q = $q->map(function ($row) {
                return [
                    // 'date' => 
                    // Carbon::parse($row->created_at)->setTimezone('Asia/Bangkok')->format('Y-m-d'),
                    DateUtility::DMY($row->created_at),
                    // 'time' => 
                    Carbon::parse($row->created_at)->format('H:i:m'),
                    // 'product_name' => 
                    $row->name,
                    // 'instock' => 
                    in_array($row['type'], [StockLogs::INSTOCK, StockLogs::TRANSFERIN]) ? $row['amount'] : '0',
                    // 'outstock' => 
                    in_array($row['type'], [StockLogs::OUTSTOCK, StockLogs::TRANSFEROUT]) ? $row['amount'] : '0',
                    // 'total' => 
                    str($row->total),
                    // 'created_name' => 
                    $row->created_name,
                    //detail
                    // 'from' => 
                    in_array($row['type'], [StockLogs::INSTOCK, StockLogs::OUTSTOCK]) ? ($row->detail['shelter_name'] ?? ($row->detail['other'] ?? null)) : ($row->detail['form_name'] ?? null),
                    // 'reason' => 
                    $row->detail['reason'] ?? '',
                    // 'lot_no' => 
                    $row->detail['lot_no'] ?? '',
                    // 'date_exp' =>  
                    // isset($row->detail['exp_date']) ? Carbon::parse($row->detail['exp_date'])->setTimezone('Asia/Bangkok')->format('Y-m-d') : null,
                    isset($row->detail['exp_date']) ? DateUtility::DMY($row->detail['exp_date']) : null,
                    // 'to' => 
                    // in_array($row['type'], [StockLogs::TRANSFERIN, StockLogs::TRANSFEROUT]) ? ($row->detail['recipient_name'] ?? null) : ($row->detail['to_name'] ?? null),
                    isset($row->detail['recipient_name']) ? $row->detail['recipient_name'] : ($row->detail['to_name'] ?? null),
                    // 'sender_name' => 
                    $row->detail['sender_name'] ?? '',
                ];
            })->toArray();

            $data[] = new StockExport(
                $item['name'],
                [
                    'วันที่กระทำ',
                    'เวลา',
                    'ชื่อทรัพยากร',
                    'นำเข้า',
                    'ใช้ไป',
                    'สรุปยอด',
                    'ชื่อผู้กระทำ',
                    'ผู้โอน/แหล่งที่มา',
                    'หมายเหตุ',
                    'Lot No',
                    'วันที่ ผลิต/หมดอายุ',
                    'ผู้รับ',
                    'ผู้จ่ายของ'
                ],
                $q
            );
        }
        return Excel::download(new MultiSheetExport($data), 'Stock-Report.xlsx');
    }
}
