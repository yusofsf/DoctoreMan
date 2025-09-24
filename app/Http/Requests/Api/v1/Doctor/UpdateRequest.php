<?php

namespace App\Http\Requests\Api\v1\Doctor;

use Illuminate\Foundation\Http\FormRequest;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'city' => 'required|string|max:255',
            'specialty' => 'required|string|max:255',
            'medical_code' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'consultation_fee' => 'required|numeric',
            'consultation_duration' => 'required|integer',
        ];
    }
}
