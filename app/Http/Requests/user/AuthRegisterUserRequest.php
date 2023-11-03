<?php

namespace App\Http\Requests\user;

use Illuminate\Foundation\Http\FormRequest;

class AuthRegisterUserRequest extends FormRequest
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
            'email' => 'required|unique:users,email|max:255|regex:/^[\w\.-]+@[\w\.-]+\.\w+$/',
            'username' => 'required|min:8',
            'name' => 'required|min:8',
            'gender' => 'required',
            'password' => 'required|min:8|regex:/^(?=.*[A-Z])(?=.*\d).+/',
        ];
    }

    public function messages(): array
    {
        return [
            'email.regex' => 'Invalid email',
            'password.regex' => 'Invalid password'
        ];
    }
}
