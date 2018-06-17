<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class NodeTransaction
 * @package App\Http\Resources
 */
class NodeTransactionResource extends JsonResource
{
    public static function fromArray($transaction)
    {
        // TODO: Implement
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
