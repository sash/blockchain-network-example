<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NodeBlock extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        
        return [
                'index' => $this->index,
                'difficulty' => $this->difficulty,
                'mined_by_address' => $this->mined_by_address,
                'previous_block_hash' => $this->previous_block_hash,
                'data_hash' => $this->data_hash,
                'nonce' => $this->nonce,
                'timestamp' => $this->timestamp,
                'block_hash' => $this->block_hash,
                'transactions' => $this->transactions->map(function($transaction){return $transaction->hash;}),
                'chain_id' => \App\NodeBlock::getGenesisBlock()->block_hash,
        ];
    }
}
