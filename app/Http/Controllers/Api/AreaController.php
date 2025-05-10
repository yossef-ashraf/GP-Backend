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

    /**
     * Display a listing of areas.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $areas = Area::all();
        return $this->successResponse($areas, 'Areas retrieved successfully');
    }

    /**
     * Store a newly created area.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:areas,name',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422, $validator->errors());
        }

        $area = Area::create($request->all());
        return $this->successResponse($area, 'Area created successfully', 201);
    }

    /**
     * Display the specified area.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $area = Area::find($id);
        
        if (!$area) {
            return $this->errorResponse('Area not found', 404);
        }
        
        return $this->successResponse($area, 'Area retrieved successfully');
    }

    /**
     * Update the specified area.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $area = Area::find($id);
        
        if (!$area) {
            return $this->errorResponse('Area not found', 404);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:areas,name,' . $id,
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422, $validator->errors());
        }

        $area->update($request->all());
        return $this->successResponse($area, 'Area updated successfully');
    }

    /**
     * Remove the specified area.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $area = Area::find($id);
        
        if (!$area) {
            return $this->errorResponse('Area not found', 404);
        }
        
        $area->delete();
        return $this->successResponse(null, 'Area deleted successfully');
    }
}