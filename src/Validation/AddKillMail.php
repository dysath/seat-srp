<?php

namespace Denngarr\Seat\SeatSrp\Validation;

use Illuminate\Foundation\Http\FormRequest;

class AddKillMail extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'srpCharacterName' => 'required|string',
            'srpKillId' => 'unique:seat_srp_srp,kill_id|required|integer',
            'srpKillToken' => 'required|string',
            'srpCost' => 'numeric',
            'srpShipType' => 'string',
            'srpTypeId' => 'required|integer',
            'srpPingContent' => 'nullable|string',
        ];
    }
}
