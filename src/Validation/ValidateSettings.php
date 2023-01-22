<?php

namespace Denngarr\Seat\SeatSrp\Validation;

use Illuminate\Foundation\Http\FormRequest;

class ValidateSettings extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'webhook_url'   => 'url|present|nullable',
            'mention_role'  => 'string|present|nullable',
            'srp_method'    => 'present|in:0,1|nullable',
            'srp_price_cap' => 'required|integer',
        ];
    }
}
