<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of addresses.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $addresses = Address::with(['user', 'area'])->get();
        return $this->successResponse($addresses, 'Addresses retrieved successfully');
    }

    /**
     * Store a newly created address.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'area_id' => 'required|exists:areas,id',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'building_number' => 'required|string|max:255',
            'apartment_number' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422, $validator->errors());
        }

        $address = Address::create($request->all());
        return $this->successResponse($address, 'Address created successfully', 201);
    }

    /**
     * Display the specified address.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $address = Address::with(['user', 'area'])->find($id);
        
        if (!$address) {
            return $this->errorResponse('Address not found', 404);
        }
        
        return $this->successResponse($address, 'Address retrieved successfully');
    }

    /**
     * Update the specified address.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $address = Address::find($id);
        
        if (!$address) {
            return $this->errorResponse('Address not found', 404);
        }
        
        $validator = Validator::make($request->all(), [
            'user_id' => 'sometimes|required|exists:users,id',
            'area_id' => 'sometimes|required|exists:areas,id',
            'state' => 'sometimes|required|string|max:255',
            'zip_code' => 'sometimes|required|string|max:255',
            'street' => 'sometimes|required|string|max:255',
            'building_number' => 'sometimes|required|string|max:255',
            'apartment_number' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422, $validator->errors());
        }

        $address->update($request->all());
        return $this->successResponse($address, 'Address updated successfully');
    }

    /**
     * Remove the specified address.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $address = Address::find($id);
        
        if (!$address) {
            return $this->errorResponse('Address not found', 404);
        }
        
        $address->delete();
        return $this->successResponse(null, 'Address deleted successfully');
    }

    /**
     * Get addresses for a specific user.
     *
     * @param  int  $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserAddresses($userId)
    {
        $addresses = Address::where('user_id', $userId)->with('area')->get();
        return $this->successResponse($addresses, 'User addresses retrieved successfully');
    }
}