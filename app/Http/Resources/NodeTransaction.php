<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NodeTransaction extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /**
         *
         * @property string $senderAddress
         * @property string $receiverAddress
         * @property int $value
         * @property int $fee
         * @property string $data
         * @property string $hash
         * @property string $senderPublicKey
         * @property string $signature
         * @property boolean|null $transferSuccessful
         * @property int|null $block_id
         * @property Carbon $created_at
         * @property int $senderSequence
         * @property int $sequence
         * @property NodeBlock|null $block
         */
        /**
         * $transactionRepresentation['from'] = $transaction->senderAddress;
         * $transactionRepresentation['to'] = $transaction->receiverAddress;
         * $transactionRepresentation['value'] = $transaction->value;
         * $transactionRepresentation['fee'] = $transaction->fee;
         * $transactionRepresentation['datetime'] = $transaction->created_at->getTimestamp();
         * $transactionRepresentation['public_key'] = $transaction->senderPublicKey;
         */
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
            'timestamp' => $this->created_at->timestamp,
        ];
    }
}
