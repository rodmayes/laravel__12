<?php

namespace App\Http\Requests\Playtomic;

use App\Models\Resource;
use Illuminate\Foundation\Http\FormRequest;

class ResourceStoreRequest extends FormRequest
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
            'playtomic_id' => 'required|string|max:150|unique:' . Resource::class.',playtomic_id',
            'priority' => 'required|integer|min:0',
            'club_id' =>  'required|boolean',
            'visible' =>  'boolean'
        ];
    }
}
