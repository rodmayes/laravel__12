<?php

namespace App\Http\Requests\Playtomic;

use App\Models\Resource;
use Illuminate\Foundation\Http\FormRequest;

class BookingUpdateRequest extends FormRequest
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
            'club_id' => ['integer', 'exists:playtomic_club,id', 'required'],
            'resources' => ['array', 'required'],
            'timetables' => ['array', 'required'],
            'started_at' => ['nullable'],
            'public' => ['nullable'],
            'booking_preference' => ['nullable'],
            'player_email' => ['string', 'exists:users,email', 'required'],
            'duration' => ['integer']
        ];
    }
}
