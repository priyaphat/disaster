<?php

namespace App\Services;

use App\Enums\ActionType;
use App\Http\Resources\TransferHistoryResource;
use App\Models\Population;
use App\Models\Team;
use App\Models\TransferHistory;
use Illuminate\Http\Request;

class TransferHistoryService
{
    public function createLog($action_type, $table, $id, $table2, $id2)
    {
        $tableData = [
            'action_type' => $action_type,
            'parentable_type' => $table,
            'parentable_id' => $id,
            'childable_type' => $table2,
            'childable_id' => $id2
        ];

        TransferHistory::create($tableData);
    }

    public function getlog(array $filters = [], $table, $id)
    {
        $data = TransferHistory::filter($filters)->where('parentable_type', $table)->where('parentable_id', $id)->with('created_by_name');

        if ($table === Population::class) {
            $data->with('shelter');
        }
        if ($table === Team::class) {
            $data->with('team');
        }
        return $data = $data->paginate(10);
        
        return $data = $data->setCollection(TransferHistoryResource::collection($data->items())->collection);
    }

    public function getactiontype()
    {
        return array_map(fn($case) => [
            'id' => $case->value,
            'name' => $case->action_type(),
        ], ActionType::cases());
    }
}
