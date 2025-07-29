<?php

namespace App\Services;

use Carbon\Carbon;

class SearchService
{
    public function search($q, $f, $queryby = null, $skip = [])
    {
        if (!empty($f['name'])  && !in_array(1, $skip)) {
            $q->where('name', 'like', '%' . $f['name'] . '%');
        }
        if (!empty($f['search']) && !in_array(2, $skip)) {
            $q->where('name', 'like', '%' . $f['search'] . '%');
        }
        if (!empty($f['id'])  && !in_array(3, $skip)) {
            $queryby = 'first';
            $q->where('id', $f['id']);
        }
        if (!empty($f['category_id'])  && !in_array(4, $skip)) {
            $q->where('category_id', $f['category_id']);
        }
        if (!empty($f['unit_id'])  && !in_array(5, $skip)) {
            $q->where('unit_id', $f['unit_id']);
        }
        if (!empty($f['sort'])  && !in_array(6, $skip)) {
            $q->orderBy('created_at', $f['sort'] == 'asc' ? 'asc' : 'desc');
        }
        if (!empty($f['start_date']) && !empty($f['end_date']  && !in_array(7, $skip))) {
            $q->whereBetween('created_at', [
                Carbon::parse($f['start_date'])->startOfDay(),
                Carbon::parse($f['end_date'])->endOfDay()
            ]);
        }

        if (!empty($f['paginate'])) {
            return $q->paginate($f['paginate'] ?? 10);
        } else {
            if ($queryby == 'first') return $q->first();
            if ($queryby == 'get') return $q->get();
        }

        return $q;
    }
}
