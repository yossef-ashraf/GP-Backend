<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AreaController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $areas = Area::all();
        return $this->successResponse($areas, 'Areas retrieved successfully');
    }

    public function show($id)
    {
        $area = Area::find($id);
        return $this->successResponse($area, 'Area retrieved successfully');
    }

}