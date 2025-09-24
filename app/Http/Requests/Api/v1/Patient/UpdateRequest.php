<?php

namespace App\Http\Requests\Api\v1\Patient;

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
        return $this->user()->isAdministrator() || $this->user()->isPatient();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'gender' => ['required', Rule::in(['مرد', 'زن'])],
            'birthdate' => 'required|date',
            'phone_number' => 'string|max:255'
        ];
    }
}
