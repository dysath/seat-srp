<?php

namespace Denngarr\Seat\SeatSrp\Validation;

use Illuminate\Foundation\Http\FormRequest;

class AddReason extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'srpKillId' => 'exists:seat_srp_srp,kill_id|required|integer',
            'srpReasonContent' => 'nullable|string',
        ];
    }
}
