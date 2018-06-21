<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitMinedBlock extends FormRequest
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
            'block.index' => 'required',
            'block.difficulty' => 'required',
            'block.cumulativeDifficulty' => 'required',
            'block.mined_by_address' => 'required',
            'block.previous_block_hash' => 'required',
            'block.nonce' => 'required',
            'block.timestamp' => 'required',
            'block.transactions' => 'required',
            'block.chain_id' => 'required',
        ];
    }
}
