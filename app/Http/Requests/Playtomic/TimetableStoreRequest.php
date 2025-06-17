<?php

namespace App\Http\Requests\Playtomic;

use App\Models\Club;
use Illuminate\Foundation\Http\FormRequest;

class TimetableStoreRequest extends FormRequest
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
            'playtomic_id' => 'required|string|max:150',
            'playtomic_id_summer' => 'required|string|max:150',
        ];
    }
}
