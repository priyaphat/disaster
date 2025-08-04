<?php

namespace App\Services;

use App\Models\Unit;
use App\Services\SearchService;

class UnitService
{

    protected $search;

    public function __construct(SearchService $search)
    {
        $this->search = $search;
    }
    public function index(array $filters = [])
    {
        $query = Unit::select('units.*')
            ->selectSub(function ($q) {
                $q->from('products')
                    ->select('id')
                    ->whereColumn('products.unit_id', 'units.id')
                    ->orderBy('created_at', 'desc')
                    ->limit(1);
            }, 'used');
        return $this->search->search($query, $filters, 'get');
    }

    public function create(array $data): Unit
    {
        return Unit::create($data);
    }

    public function update(Unit $unit, array $data): Unit
    {
        $unit->update($data);
        return $unit;
    }
}
