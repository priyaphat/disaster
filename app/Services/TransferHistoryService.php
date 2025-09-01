<?php

namespace App\Services;

use App\Enums\ActionType;
use App\Http\Resources\TransferHistoryResource;
use App\Http\Utility\LogUtility;
use App\Models\Population;
use App\Models\Shelter;
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

    public function updateLog($childTable, $childId, $childName)
    {
        return TransferHistory::where(['childable_type' => $childTable, 'childable_id' => $childId])
            ->update(['childable_name' => $childName]);
    }

    public function getLog(array $filters = [], $table, $id)
    {
        $data = TransferHistory::filter($filters)->where('parentable_type', $table)->where('parentable_id', $id)->with('childable');
        $data = $data->paginate(10);

        return $data = $data->setCollection(TransferHistoryResource::collection($data->items())->collection);
    }

    public function getShelterlog(array $filters = [], bool $paginate = true, int $perPage = 10)
    {
        $query = TransferHistory::filter($filters)->where('parentable_type', Shelter::class)->with('created_by_name', 'shelter');
        if (!$paginate) {
            return TransferHistoryResource::collection($query->get());
        } else {
            $data = $query->paginate($perPage);
            $paginate = $data->setCollection(TransferHistoryResource::collection($data->items())->collection);
            return $paginate;
        }
    }

    public function getActiontype($model)
    {
        switch ($model) {
            case 'user':
                $enum = array_slice(ActionType::cases(), 0, 6);
                break;
            case 'population':
                $enum = array_slice(ActionType::cases(), 0, 3);
                break;
            default:
                $enum = array_slice(ActionType::cases(), 6, 8);
        }

        return array_map(function ($case) use ($model) {
            $class = LogUtility::getClassName($model);
            return [
                'id' => $case->value,
                'name' => $case->label($class)
            ];
        }, $enum);
    }
}
