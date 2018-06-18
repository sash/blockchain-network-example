<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTransaction;
use App\Http\Resources\NodeTransactionResource;
use App\JsonError;
use App\Node\BalanceFactory;
use App\Node\Broadcast;
use App\NodeTransaction;
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
     * @todo: Use json in body instead of a form
     */
    public function postTransaction(CreateTransaction $request, Broadcast $broadcast)
    {
        try {
            $transaction = NodeTransactionResource::fromRequest($request);

            $this->transactionValidator->assertValid($transaction);

            // The balance can always be OK based on another parallel chain that we yet don't know about
            // $balanceFactory->forCurrentPending()->addTransaction($transaction); // assets funds
            
            $transaction->save();
            $broadcast->newTransaction($transaction);

            return new NodeTransactionResource($transaction);
            
        } catch (\Exception $ex){
            return JsonError::fromException($ex)->response(422);
        }
    }
}
