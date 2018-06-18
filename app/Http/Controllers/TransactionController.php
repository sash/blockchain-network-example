<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTransaction;
use App\Http\Resources\NodeTransactionResource;
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
     */
    public function postTransaction(CreateTransaction $request)
    {
        try {
            $transaction = NodeTransactionResource::fromRequest($request);

            if ($this->transactionValidator->assertValid($transaction)) {
                $transaction->save();

                return new NodeTransactionResource($transaction);
            };
        } catch (InvalidTransaction $ex){
            return response(['errors'=> $ex->getMessage()], 422);
        }
    }
}
