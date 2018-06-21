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
        $addresses = str_split($address, 40);
        $res = [];
        foreach ($addresses as $address){
            $unconfirmed = $this->transactionRepository->balanceForAddress($address);
            $confirmed = $this->transactionRepository->balanceForAddress($address, 1);
            $solid = $this->transactionRepository->balanceForAddress($address, 6);
    
    
            $res[] = [
                    'solid'   => $solid,
                    'confirmed'   => $confirmed,
                    'unconfirmed' => $unconfirmed,
                    'txs'         => $this->transactionRepository->transactionsBySender($address)->count(),
                // Totoal number of spent transactions for the address
            ];
        }
        if (count($res) == 1){
            return $res[0];
        }
        return $res;
    }
}
