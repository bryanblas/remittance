<?php
namespace App\Http\Requests;

use App\Http\Requests\FormRequest;

class UpdateWithdrawalRequest extends FormRequest
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
            'transaction_number' => 'required',
            'merchant_account_id' => 'required',
            'account_number' => 'required',
            'beneficiary_name' => 'required',
            'account_type' => 'required',
            'currency' => 'required',
            'amount' => 'required',
            'rate' => 'required',
            'fee' => 'required',
            'beneficiary_country' => 'required',
            'beneficiary_address' => 'required',
            'bank_name' => 'required',
            'swift_code' => 'required',
            'bank_country' => 'required',
            'bank_address' => 'required',
            'contact_number' => 'required'
        ];
    }
}
