<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\handlingResponseRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends handlingResponseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'nullable|string',
            "dob_day" => 'nullable',
            "dob_month" => 'nullable',
            "dob_year" => 'nullable',
            'phone' => 'nullable|string|max:15|unique:users,phone,' . auth()->id(),
            // 'email' => 'required|string|email|max:255|unique:users',
        ];
    }
}
