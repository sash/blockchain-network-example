<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTransaction extends FormRequest
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
            'transaction.from' => 'required|alpha_num|size:40',
            'transaction.from_id' => 'required',
            'transaction.to' => 'required|alpha_num|size:40',
            'transaction.value' => 'required|integer|min:0',
            'transaction.fee' => 'required|integer',
            'transaction.hash' => 'required|alpha_num|size:64|unique:node_transactions,hash',
            'transaction.signature' => 'required|alpha_num|size:130'
        ];
    }
}


