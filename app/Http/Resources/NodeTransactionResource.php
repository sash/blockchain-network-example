<?php

namespace App\Http\Resources;

use App\Crypto\TransactionHasher;
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
            'timestamp' => $request->get('timestamp')
        ]);
    }
    
    
    /**
     * @param $transaction
     * @return NodeTransaction
     * @deprecated Use fromRequest
     */
    public static function fromArray($transaction){
        $res = new NodeTransaction();
        $res->senderAddress = $transaction['from'];
        $res->senderSequence = $transaction['from_id'];
        $res->receiverAddress = $transaction['to'];
        $res->value = $transaction['value'];
        $res->fee = $transaction['fee'];
        $res->data = $transaction['data'];
        $res->timestamp = $transaction['timestamp'];
        $res->hash = app(TransactionHasher::class)->getHash($res);
        $res->signature = $transaction['signature'];
        return $res;
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
            'timestamp' => $this->timestamp,
            'mined_in_block_index' => $this->block_id ? $this->block->index : null,
            'hash' => $this->hash,
            'signature' => $this->signature,
        ];
    }
}
