<?php

namespace App\Http\Utility;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PaginateUtility
{

    public static function Paginate($items, $perPage = 10, $page = null, $options = [])
    {
        $page = $page ?: (LengthAwarePaginator::resolveCurrentPage() ?: 1);

        if ($items instanceof Collection) {
            $items = $items->all();
        }

        $items = array_values($items);

        $total = count($items);
        $results = array_slice($items, ($page - 1) * $perPage, $perPage);

        $options = array_merge([
            'path' => request()->url(),
            'query' => request()->query(),
        ], $options);

        return new LengthAwarePaginator(
            $results,
            $total,
            $perPage,
            $page,
            $options
        );
    }
}
