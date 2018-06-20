<?php

namespace App\Http\Controllers;

use App\Crypto\PublicPrivateKeyPair;
use App\Node\PeerClient;
use App\Node\TransactionFactory;
use Illuminate\Http\Request;

class FaucetController extends Controller
{
    public function getFaucet(){
        return view('faucet');
    }
    public function postFaucet(Request $request, TransactionFactory $factory){
        $address = $request->get('address');
        $host = $request->get('host');
        $key = PublicPrivateKeyPair::fromPrivateKey($_ENV['PRIVATE_KEY']);
        $client = new \App\Faucet\PeerClient($host);
        
        $balance = $client->getBalance($key->getAddress());
        
        
        $client->postTransaction($factory->buildSpendTransaction($key, $balance['txs'], 1000000, 10, $address, 'Free monet from faucet'));
        return redirect('/');
    }
}
