<?PHP

namespace Denngarr\Seat\SeatSrp\Validation;

use Illuminate\Foundation\Http\FormRequest;

class AddKillMail extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'srpCharacterName' => 'required|string',
            'srpKillId' => 'unique:seat_srp_srp,kill_id|required|integer',
            'srpKillToken' => 'required|string',
            'srpCost' => 'numeric',
            'srpShipType' => 'string',
	        'srpTypeId' => 'required|integer',
	        'srpPingContent' => 'string'
        ];
    }
}

