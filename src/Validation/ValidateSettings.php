<?php

namespace Denngarr\Seat\SeatSrp\Validation;

use Illuminate\Foundation\Http\FormRequest;

class ValidateSettings extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'webhook_url'    => 'url|present|nullable',
            'mention_role'   => 'string|present|nullable',
        ];
    }
}
