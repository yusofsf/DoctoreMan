<?php

namespace App\Http\Requests\Api\v1\User;

use App\Enums\UserRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isAdministrator();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'user_name' => 'required|string|max:255',
            'email' => 'required|email:rfc',
            'role' => [
                'required',
                Rule::enum(UserRole::class)
            ],
            'phone' => 'string|max:255',
            'address' => 'string|max:255',
            'bio' => 'string|max:255',
            'city' => 'string|max:255',
            'specialization' => 'string|max:255',
            'session_duration' => 'integer'
        ];
    }
}
