<?php

namespace App\Http\Requests\user;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            "username" => "required|min:8",
            "name" => "required|min:8",
            "password" => "required|min:8|regex:/^(?=.*[A-Z])(?=.*\d).+/",
            "gender" => "required",
        ];
    }

    public function messages(): array
    {
        return [
            'password.regex' => 'Invalid password'
        ];
    }
}
