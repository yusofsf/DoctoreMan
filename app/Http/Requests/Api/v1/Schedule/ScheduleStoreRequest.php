<?php

namespace App\Http\Requests\Api\v1\Schedule;

use App\Enums\DayOfWeek;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ScheduleStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isPatient();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'day_of_week' => [
                'required',
                Rule::enum(DayOfWeek::class)
            ],
            'start_time' => [
                'required',
                Rule::date()->format('H:i:s')
            ]
        ];
    }
}
