<?php

namespace App\Http\Requests\Api\v1\User;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'user_name' => 'required|string|max:255',
            'email' => 'required|email:rfc',
            'role' => [
                'required',
                Rule::enum(UserRole::class)
            ],
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'bio' => 'string|max:255',
            'city' => 'string|max:255',
            'specialization' => 'string|max:255',
            'session_duration' => 'integer'
        ];
    }
}
