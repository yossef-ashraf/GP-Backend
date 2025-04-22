<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\handlingResponseRequest;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends handlingResponseRequest
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
            'phone' => 'required|string|max:15', // Assuming max length for phone
            // 'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
            'gender' => 'nullable|string',
            'dob_day' => 'nullable|string',
            'dob_month' => 'nullable|string',
            'dob_year' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'name_is_required',
            'phone.required' => 'phone_is_required',
            'email.required' => 'email_is_required',
            'password.required' => 'password_is_required',
            'password.min' => 'password_must_be_at_least_6_characters',
            'gender.required' => 'gender_is_required',
            'dob_day.required' => 'dobDay_is_required',
            'dob_month.required' => 'dobMonth_is_required',
            'dob_year.required' => 'dobYear_is_required',
        ];
    }
}
