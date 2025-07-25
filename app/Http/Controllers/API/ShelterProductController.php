<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShelterProductRequest;
use App\Models\ShelterProduct;
use Illuminate\Http\Request;

class ShelterProductController extends Controller
{
    public function store(ShelterProductRequest $request)
    {
        return ShelterProduct::create($request);
    }
}
