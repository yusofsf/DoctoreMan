<?php

namespace App\Http\Requests\Api\v1\Auth;


use App\Enums\UserRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class RegisterRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'string|required|max:255',
            'last_name' => 'string|required|max:255',
            'user_name' => 'string|required|max:255',
            'role' => [Rule::enum(UserRole::class)],
            'email' => 'string|required|unique:users,email|email:rfc',
            'password' => 'string|required|min:6|confirmed',
        ];
    }
}
