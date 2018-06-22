<?php

namespace App\Http\Controllers;

use App\Crypto\TransactionHasher;
use App\Http\Requests\CreateTransaction;
use App\Http\Resources\NodeTransactionResource;
use App\JsonError;
use App\Node\BalanceFactory;
use App\Node\Broadcast;
use App\NodeTransaction;
use App\Repository\BalanceRepository;
use App\Repository\TransactionRepository;
use App\Validators\TransactionValidator;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * @var \App\Validators\TransactionValidator
     */
    private $transactionValidator;

    /**
     * TransactionController constructor.
     *
     * @param \App\Validators\TransactionValidator $transactionValidator
     *
     * @internal param \App\Validators\TransactionValidator $validator
     */
    public function __construct(TransactionValidator $transactionValidator)
    {
        $this->transactionValidator = $transactionValidator;
    }

    /**
     * @param \App\Http\Requests\CreateTransaction $request
     *
     * @return \App\Http\Resources\NodeTransactionResource|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     *
     */
    public function postTransaction(CreateTransaction $request, Broadcast $broadcast, BalanceFactory $balanceFactory, BalanceRepository $balanceRepository, TransactionHasher $hasher)
    {
        try {
            $transaction = NodeTransactionResource::fromArray(@json_decode($request->getContent(), true)['transaction']);
            
            $transaction->hash = $hasher->getHash($transaction);
            
//            $log = [];
            $this->transactionValidator->assertValid($transaction);
//            $log[] = "Transaction is valid";

            // The balance can always be OK based on another parallel chain that we yet don't know about
            try{
                $balance = $balanceFactory->forCurrentPending();
//                $log[] = $balance->balance;
                $balance->addTransaction($transaction); // assets funds and cound throw exception that must be ignored
                $balance->savePending();
//                $log[] = "Balance updated";
            } catch (\Exception $e){
                error_log("Not enought funds to update pending balance (But the transaction is still accepted): " . $e->getMessage());
//                $log[] = "Not enought funds: ".$e->getMessage();
                // Ignore missing funds
            }
            
            $transaction->save();
            $broadcast->newTransaction($transaction);

            return response(json_encode(['balance' => $balance->getForAddress($transaction->senderAddress)]), 201);
//            return $log;
            
        } catch (\Exception $ex){
            return JsonError::fromException($ex)->response(422);
        }
    }
    
    public function getTransaction($hash, TransactionRepository $transactionRepository){
        try{
            $transaction = $transactionRepository->transactionsByHash($hash);
            if ($transaction){
                return new NodeTransactionResource($transaction);
            } else {
                return response('', 404);
            }
        } catch (\Throwable $exception){
            return JsonError::fromException($exception)->response(422);
        }
    }
    
    public function getTransactionsForAddress($address, TransactionRepository $transactionRepository){
        $addresses = str_split($address, 40);
        return NodeTransactionResource::collection($transactionRepository->transactionsForAddress($addresses));
    }
}
