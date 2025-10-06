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
                'reason' => $item['reason'],
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
        // $sheets = [
        //     new StockExport(
        //         'อุปโภคบริโภค',
        //         ['วันที่กระทำ', 'เวลา', 'ชื่อทรัพยากร', 'นำเข้า', 'ใช้ไป', 'สรุปยอด', 'ชื่อผู้กระทำ', 'ผู้โอน/แหล่งที่มา', 'ผู้รับ', 'หมายเหตุ', 'Lot No', 'วันที่ ผลิต/หมดอายุ', 'ผู้จ่ายของ'],
        //         [
        //             ['2025-10-01', '10:20', 'ข้าวสาร', 50, 20, 30, 'สมชาย'],
        //             ['2025-10-01', '14:10', 'น้ำดื่ม', 100, 25, 75, 'สมหญิง'],
        //         ]
        //     ),
        //     new StockExport(
        //         'เครื่องจักร เครื่องมือต่างๆ',
        //         ['วันที่กระทำ', 'เวลา', 'ชื่อทรัพยากร', 'นำเข้า', 'ใช้ไป', 'สรุปยอด', 'ชื่อผู้กระทำ', 'ผู้โอน/แหล่งที่มา', 'ผู้รับ', 'หมายเหตุ', 'Lot No', 'วันที่ ผลิต/หมดอายุ', 'ผู้จ่ายของ'],
        //         [
        //             ['2025-10-02', '09:00', 'หน้ากากอนามัย', 500, 120, 380, 'หมอเอก'],
        //             ['2025-10-02', '09:30', 'ถุงมือยาง', 200, 50, 150, 'หมอแอน'],
        //         ]
        //     ),
        //     new StockExport(
        //         'ยารักษอุปกรณ์ทางการแพทย์าโรค',
        //         ['วันที่กระทำ', 'เวลา', 'ชื่อทรัพยากร', 'นำเข้า', 'ใช้ไป', 'สรุปยอด', 'ชื่อผู้กระทำ', 'ผู้โอน/แหล่งที่มา', 'ผู้รับ', 'หมายเหตุ', 'Lot No', 'วันที่ ผลิต/หมดอายุ', 'ผู้จ่ายของ'],
        //         [
        //             ['2025-10-02', '11:00', 'ยาพาราเซตามอล', 300, 80, 220, 'เภสัชกร'],
        //             ['2025-10-02', '11:20', 'ยาแก้อักเสบ', 200, 40, 160, 'เภสัชกร'],
        //         ]
        //     ),
        //     new StockExport(
        //         'ยารักษาโรค',
        //         ['วันที่กระทำ', 'เวลา', 'ชื่อทรัพยากร', 'นำเข้า', 'ใช้ไป', 'สรุปยอด', 'ชื่อผู้กระทำ', 'ผู้โอน/แหล่งที่มา', 'ผู้รับ', 'หมายเหตุ', 'Lot No', 'วันที่ ผลิต/หมดอายุ', 'ผู้จ่ายของ'],
        //         [
        //             ['2025-10-02', '11:00', 'ยาพาราเซตามอล', 300, 80, 220, 'เภสัชกร'],
        //             ['2025-10-02', '11:20', 'ยาแก้อักเสบ', 200, 40, 160, 'เภสัชกร'],
        //         ]
        //     ),
        // ];

        $group = app(ProductController::class)->typeName(true);
        $data = [];
        $shelter = $r['shelter_id'] ?? ProductUtility::getShelter();
        foreach ($group as $item) {
            $req = new Request();
            $req['shelter_id'] = $shelter;
            $req['get'] = true;
            if ($item['id']) $req['group_id'] = $item['id'];
            if ($r['start_date'] && $r['end_date']) {
                $req['start_date'] = $r['start_date'];
                $req['end_date'] = $r['end_date'];
            }
            $q = self::index($req);

            $q = $q->map(function ($row) {
                return [
                    'date' => Carbon::parse($row->created_at)->format('Y-m-d'),
                    'time' => Carbon::parse($row->created_at)->format('H:i'),
                    'product_name' => $row->name,
                    // 'import' => $row->type == StockLogs::IMPORT ? $row->amount : '',
                    // 'export' => $row->type == StockLogs::EXPORT ? $row->amount : '',
                    'total' => $row->total,
                    'created_name' => $row->created_name,
                    'from' => is_array($row->detail) ? ($row->detail['form_name'] ?? '') : '',
                    'to' => is_array($row->detail) ? ($row->detail['to_name'] ?? '') : '',
                    'remark' => $row->remark ?? '',
                    'lot_no' => $row->lot_no ?? '',
                    'date_exp' => $row->date_exp ?? '',
                    'giver' => $row->giver ?? '',
                ];
            });
            return $q;
            $data[] = new StockExport(
                $item['name'],
                ['วันที่กระทำ', 'เวลา', 'ชื่อทรัพยากร', 'นำเข้า', 'ใช้ไป', 'สรุปยอด', 'ชื่อผู้กระทำ', 'ผู้โอน/แหล่งที่มา', 'ผู้รับ', 'หมายเหตุ', 'Lot No', 'วันที่ ผลิต/หมดอายุ', 'ผู้จ่ายของ'],
                [
                    ['2025-10-02', '11:00', 'ยาพาราเซตามอล', 300, 80, 220, 'เภสัชกร'],
                    ['2025-10-02', '11:20', 'ยาแก้อักเสบ', 200, 40, 160, 'เภสัชกร'],
                ]
            );
        }

        // return Excel::download(new MultiSheetExport($sheets), 'Stock-Report.xlsx');
    }
}
