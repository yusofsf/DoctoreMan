<?php

namespace App\Http\Requests\Api\v1\Doctor;

use App\Enums\DayOfWeek;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $validDays = collect(DayOfWeek::cases())->pluck('value')->toArray();

        return [
            'city' => 'required|string|max:255',
            'specialty' => 'required|string|max:255',
            'medical_code' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'consultation_fee' => 'required|numeric',
            'consultation_duration' => 'required|integer',
            'working_days' => [
                'required',
                'array',
                function ($attribute, $value, $fail) use ($validDays) {
                    if (!is_array($value)) {
                        $fail('روز‌های کاری باید به صورت آرایه باشد.');
                        return;
                    }

                    foreach (array_keys($value) as $day) {
                        if (!in_array($day, $validDays)) {
                            $fail("روز '{$day}' نامعتبر است. روز‌های معتبر: " . implode(', ', $validDays));
                        }
                    }
                }
                ]
        ];
    }
}
