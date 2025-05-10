<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $users = User::with('addresses')->get();
        return $this->successResponse($users, 'Users retrieved successfully');
    }

    /**
     * Store a newly created user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'gender' => 'nullable|string',
            'dob_day' => 'nullable|integer|between:1,31',
            'dob_month' => 'nullable|integer|between:1,12',
            'dob_year' => 'nullable|integer|min:1900',
            'phone' => 'nullable|string|max:15|unique:users',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422, $validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'gender' => $request->gender,
            'dob_day' => $request->dob_day,
            'dob_month' => $request->dob_month,
            'dob_year' => $request->dob_year,
            'phone' => $request->phone,
        ]);

        return $this->successResponse($user, 'User created successfully', 201);
    }

    /**
     * Display the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = User::with('addresses')->find($id);
        
        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }
        
        return $this->successResponse($user, 'User retrieved successfully');
    }

    /**
     * Update the specified user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:8',
            'gender' => 'nullable|string',
            'dob_day' => 'nullable|integer|between:1,31',
            'dob_month' => 'nullable|integer|between:1,12',
            'dob_year' => 'nullable|integer|min:1900',
            'phone' => 'nullable|string|max:15|unique:users,phone,' . $id,
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422, $validator->errors());
        }

        $data = $request->except('password');
        
        if ($request->has('password')) {
            $data['password'] = Hash::make($request->password);
        }
        
        $user->update($data);
        
        return $this->successResponse($user, 'User updated successfully');
    }

    /**
     * Remove the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return $this->errorResponse('User not found', 404);
        }
        
        $user->delete();
        
        return $this->successResponse(null, 'User deleted successfully');
    }
}