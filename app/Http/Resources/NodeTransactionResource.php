<?php

namespace App\Http\Resources;

use App\Crypto\TransactionHasher;
use App\Exceptions\InvalidTransaction;
use App\NodeTransaction;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class NodeTransaction
 * @package App\Http\Resources
 */
class NodeTransactionResource extends JsonResource
{
    /**
     * @param $request
     * @return NodeTransaction
     * @deprecated The request expects a post form. Posting arbitraty data seems to not work! Perhaps the problem is that the post is transaction.from and so on...
     */
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
     */
    public static function fromArray($transaction){
        if (!is_array($transaction)){
            throw new InvalidTransaction('Transaction representation is not an array! - '.$transaction);
        }
        $res = new NodeTransaction();
        if (!isset($transaction['from'])){
            throw new InvalidTransaction('Invalid transaction format: '.json_encode($transaction));
        }
        $res->senderAddress = $transaction['from'];
        $res->senderSequence = $transaction['from_id'];
        $res->receiverAddress = $transaction['to'];
        $res->value = $transaction['value'];
        $res->fee = $transaction['fee'];
        $res->data = $transaction['data'] == null ? '' : $transaction['data'];
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
            'mined_in_block_hash' => $this->block_id ? $this->block->block_hash : null,
            'hash' => $this->hash,
            'signature' => $this->signature,
        ];
    }
}
