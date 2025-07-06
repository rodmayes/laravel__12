<?php

namespace App\Http\Requests\Playtomic;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClubUpdateRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'playtomic_id' => ['nullable', 'string', 'max:150', Rule::unique('playtomic_club', 'playtomic_id')->ignore($this->route('club'))],
            'days_min_booking' => 'required|integer|min:1',
            'timetable_summer' =>  'required|boolean',
            'booking_hour' =>  'required|date_format:H:i:s',
        ];
    }
}
