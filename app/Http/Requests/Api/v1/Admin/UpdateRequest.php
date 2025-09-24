<?php

namespace App\Http\Requests\Api\v1\Admin;

use App\Enums\AdminStatus;
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
            'display_name' => 'required|string|max:255',
            'description' => 'string|max:255',
            'status' => [
                Rule::enum(AdminStatus::class),
            ],
        ];
    }
}
