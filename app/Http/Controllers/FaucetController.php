<?php

namespace App\Http\Controllers;

use App\Crypto\PublicPrivateKeyPair;
use App\Faucet\CoinFormat;
use App\Faucet\QueueRepository;
use App\FaucetQueue;
use App\Node\PeerClient;
use App\Node\TransactionFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class FaucetController extends Controller
{
    public function getFaucet(QueueRepository $repository){
        $hosts = explode(',', $_ENV['NODE_HOSTS']);
        $client = new \App\Faucet\PeerClient($hosts[0]);
        $key = PublicPrivateKeyPair::fromPrivateKey($_ENV['PRIVATE_KEY']);
        $balance = $client->getBalance($key->getAddress());
        
        $balance['unconfirmed'] = new CoinFormat($balance['unconfirmed'] - $balance['confirmed']);
        $balance['confirmed'] = new CoinFormat($balance['confirmed']);
        
        return view('faucet', ['balance' => $balance, 'address' => $key->getAddress(), 'queue' => $repository->all()]);
    }
    public function postFaucet(Request $request, TransactionFactory $factory, QueueRepository $repository){
        $address = $request->get('address');
        if(!preg_match('/^[a-z0-9]{40}$/i', $address)){
            return Redirect::to('/')->with('error', 'Invalid address');
        }
        $host = $request->get('host');
        
        $item = new FaucetQueue();
        $item->address = $address;
        $item->peer = $host;
        $repository->push($item);
        
        return redirect('/')->with('message', 'Coin was queued for sending');
    }
}
