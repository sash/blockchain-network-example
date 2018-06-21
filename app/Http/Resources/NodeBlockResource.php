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
                'transactions' => $this->transactions->map(function($transaction) use($request){
                    return (new NodeTransactionResource($transaction))->toArray($request);
                }),
                'chain_id' => app(BlockRepository::class)->getGenesisBlock()->block_hash,
        ];
    }
    
    static function fromArray($blockArray): \App\NodeBlock
    {
        $block = new \App\NodeBlock($blockArray);

        foreach($blockArray['transactions'] as $tran){
            $obj = NodeTransactionResource::fromArray($tran);
            $block->transactions[] = $obj;
        }

        $hasher = new BlockHasher();
        $hasher->updateHashes($block);
        return $block;
    }
}
