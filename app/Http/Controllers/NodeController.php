<?php

namespace App\Http\Controllers;

use App\JsonError;
use App\Node\Broadcast;
use App\NodeTransaction;
use App\Repository\BlockRepository;
use App\Validators\TransactionValidator;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Class NodeController
 * @package App\Http\Controllers
 *
 * API:
 *  - GET /node/blocks list all blocks. Used by other nodes to sync
 *  - GET /node/block/{:blockIndex}. Get Index of the block
 *  - PUT /node/transaction {from: string, to: string, value: int, fee: int, datetime: int, data: string, signature: string}
 *  -
 *  0
 */
class NodeController extends Controller
{
    public function __construct()
    {
//        $this->middleware('guest');
    }
    
    function getBlocks(BlockRepository $repository, BlockSerializer $blockSerializer)
    {
        return $repository->getAllBlocks()->map(function($block) use ($blockSerializer) {
            return $blockSerializer->serializeBlock($block);
        });
    }
    
    function putTransaction(Request $request, TransactionValidator $validator, Broadcast $broadcast)
    {
        
        try {
            $transactionInput = $request->json()->all();
            
            $transaction = $this->constructTransactionFromInput($transactionInput);
            
            $validator->isValid($transaction);
        } catch (\Throwable $e){
            return JsonError::fromException($e);
        }
        
        $transaction->save();
        
        $broadcast->newTransaction($transaction);
        
        return [
                'success' => true,
                "message" => "Your transaction is accepted",
                "hash" => $transaction->hash
        ];
    }
    
    function getTransaction(string $hash)
    {
        return [
                'success' => true,
                "message" => "Your transaction is accepted",
                "hash"    => $hash
        ];
    }
    
    /**
     * @param $transactionInput
     * @return NodeTransaction
     */
    private function constructTransactionFromInput($transactionInput): NodeTransaction
    {
        $transaction = new NodeTransaction();
        $transaction->senderAddress = $transactionInput['from'];
        $transaction->receiverAddress = $transactionInput['to'];
        $transaction->value = intval($transactionInput['value']);
        $transaction->fee = intval($transactionInput['fee']);
        $transaction->created_at = Carbon::createFromTimestamp($transactionInput['datetime']);
        $transaction->data = isset($transactionInput['data']) ?: null;
        $transaction->signature = $transactionInput['signature'];
        return $transaction;
    }
}
