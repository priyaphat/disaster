<?php

namespace App\Services;

class SearchService
{
    public function search($query, $filters, $queryby = null)
    {
        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }
        if (!empty($filters['id'])) {
            $queryby = 'first';
            $query->where('id', $filters['id']);
        }

        if (!empty($filters['paginate'])) {
            return $query->paginate($filters['paginate'] ?? 10);
        } else {
            if ($queryby == 'first') return $query->first();
            if ($queryby == 'get') return $query->get();
        }

        return $query;
    }
}
