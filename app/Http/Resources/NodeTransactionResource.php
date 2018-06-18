<?php

namespace App\Http\Resources;

use App\NodeTransaction;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class NodeTransaction
 * @package App\Http\Resources
 */
class NodeTransactionResource extends JsonResource
{
    public static function fromRequest($request)
    {
        return new NodeTransaction([
            'senderAddress' => $request->get('from'),
            'senderSequence' => $request->get('from_id'),
            'receiverAddress' => $request->get('to'),
            'sequence' => 0,
            'value' => $request->get('value'),
            'fee' => $request->get('fee'),
            'data' => empty($request->get('data')) ? "" : $request->get('data'),
            'hash' => $request->get('hash'),
            'signature' => $request->get('signature'),
            'timestamp' => $request->get('datetime')
        ]);
    }

    public static function fromArray($transaction){
        //TODO implement
    }
    
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        
        return [
            'from' => $this->senderAddress,
            'from_id' => $this->senderSequence,
            'to' => $this->receiverAddress,
            'value' => $this->value,
            'fee' => $this->fee,
            'data' => $this->data,
            'mined_in_block_index' => $this->block_id ? $this->block->index : null,
            'hash' => $this->hash,
            'signature' => $this->signature,
            'timestamp' => $this->timestamp,
        ];
    }
}
