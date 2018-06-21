<?php

namespace App\Http\Controllers;

use App\Crypto\PublicPrivateKeyPair;
use App\Faucet\CoinFormat;
use App\Node\PeerClient;
use App\Node\TransactionFactory;
use Illuminate\Http\Request;

class FaucetController extends Controller
{
    public function getFaucet(){
        $hosts = explode(',', $_ENV['NODE_HOSTS']);
        $client = new \App\Faucet\PeerClient($hosts[0]);
        $key = PublicPrivateKeyPair::fromPrivateKey($_ENV['PRIVATE_KEY']);
        $balance = $client->getBalance($key->getAddress());
        $balance['unconfirmed'] = new CoinFormat($balance['unconfirmed']);
        $balance['confirmed'] = new CoinFormat($balance['confirmed']);
        return view('faucet', ['balance' => $balance]);
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
