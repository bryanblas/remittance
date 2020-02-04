<?php
namespace App\Http\Requests;

use App\Http\Requests\FormRequest;

class StoreExchangeRateRequest extends FormRequest
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
            'currency_from' => 'required',
            'currency_to' => 'required',
            'rate' => 'required'
        ];
    }
}
