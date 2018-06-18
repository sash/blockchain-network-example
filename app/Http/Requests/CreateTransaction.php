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
            'from' => 'required|alpha_num|size:40',
            'from_id' => 'required',
            'to' => 'required|alpha_num|size:40',
            'value' => 'required|integer|min:0|max:100000',
            'fee' => 'required|integer',
            'hash' => 'required|alpha_num|size:64|unique:node_transactions',
            'signature' => 'required|alpha_num|size:130'
        ];
    }
}


