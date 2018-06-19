<?php

namespace App\Http\Controllers;

use App\Repository\TransactionRepository;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    /**
     * @var \App\Http\Controllers\TransactionRepository
     */
    private $transactionRepository;

    /**
     * BalanceController constructor.
     *
     * @param \App\Http\Controllers\TransactionRepository|\App\Repository\TransactionRepository $transactionRepository
     */
    public function __construct(TransactionRepository $transactionRepository)
    {

        $this->transactionRepository = $transactionRepository;
    }

    public function getBalance($address)
    {
        $unconfirmed = $this->transactionRepository->balanceForAddress($address);
        $confirmed = $this->transactionRepository->balanceForAddress($address, 1);

        return [
            'confirmed'=> $confirmed,
            'unconfirmed'=> $unconfirmed,
            'txs' => $this->transactionRepository->transactionsBySender($address)->count(),
        ];
    }
}
