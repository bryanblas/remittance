<?php
namespace App\Http\Requests;

use App\Http\Requests\FormRequest;

class UpdateDepositRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'account_id' => 'required',
            'currency' => 'required',
            'amount' => 'required',
            'deposit_slip' => 'required'
        ];
    }
}
