<?php

namespace App\Http\Resources;

use App\Crypto\BlockHasher;
use App\Repository\BlockRepository;
use Illuminate\Http\Resources\Json\JsonResource;

class NodeBlockResource extends JsonResource
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
                'cumulativeDifficulty' => $this->cumulativeDifficulty,
                'mined_by_address' => $this->mined_by_address,
                'previous_block_hash' => $this->previous_block_hash,
                'data_hash' => $this->data_hash,
                'nonce' => $this->nonce,
                'timestamp' => $this->timestamp,
                'block_hash' => $this->block_hash,
                'transactions' => $this->transactions->map(function($transaction){return $transaction->hash;}),
                'chain_id' => app(BlockRepository::class)->getGenesisBlock()->block_hash,
        ];
    }
    
    static function fromArray(array $block_array): \App\NodeBlock
    {
        if ($missing=array_diff([
                'index',
                'difficulty',
                'cumulativeDifficulty',
                'mined_by_address',
                'previous_block_hash',
                'nonce',
                'timestamp',
                'transactions',
                ], array_keys($block_array))){
            throw new \InvalidArgumentException("Missing block fields: ".json_encode($missing));
        }
        
        $block = new \App\NodeBlock($block_array);
        if (!is_array($block_array['transactions'])){
            throw new \InvalidArgumentException("Transactions in block are not array: " . json_encode($block_array));
        }
        $block->transactionHashes = $block_array['transactions'];
        $hasher = new BlockHasher();
        $hasher->updateHashes($block);
        return $block;
        
    }
}
