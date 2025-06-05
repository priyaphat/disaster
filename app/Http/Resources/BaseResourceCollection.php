<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BaseResourceCollection extends ResourceCollection
{
    public function paginationInformation($request, $paginated, $default)
    {
        return [
            'current_page' => $paginated['current_page'],
            'first_page_url' => $paginated['first_page_url'],
            'from' => $paginated['from'],
            'last_page'    => $paginated['last_page'],
            'last_page_url' => $paginated['last_page_url'],
            'links'        => $paginated['links'],
            'next_page_url' => $paginated['next_page_url'],
            'path' => $paginated['path'],
            'per_page'     => $paginated['per_page'],
            'prev_page_url' => $paginated['prev_page_url'],
            'to'        => $paginated['to'],
            'total'        => $paginated['total'],
            // 'data'         => $paginated['data'],
        ];
    }
}
