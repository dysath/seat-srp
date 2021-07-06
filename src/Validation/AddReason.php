<?php

namespace Denngarr\Seat\SeatSrp\Validation;

use Illuminate\Foundation\Http\FormRequest;

class AddReason extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'srpKillId' => 'exists:seat_srp_srp,kill_id|required|integer',
            'srpReasonContent' => 'nullable|string',
        ];
    }
}
