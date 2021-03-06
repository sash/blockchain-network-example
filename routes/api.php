<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

if ($_ENV['APPLICATION'] == 'node') {
    // Node -> Node (P2P)
    Route::middleware('api')->get('/broadcast/peers', 'BroadcastController@getPeers'); // [host1, ...]
    Route::middleware('api')->post('/broadcast/peer', 'BroadcastController@postPeer');// body: ['peer': Peer]
    
    Route::middleware('api')->get('/blocks/last', 'BlockController@getLastBlock'); // Block
    Route::middleware('api')->get('/blocks', 'BlockController@getBlocks'); // Block[] + Transactions[]
    
    Route::middleware('api')->post('/broadcast/transaction',
            'BroadcastController@postTransaction'); // body: ['transaction': TxHash, 'peer' => 'source host']
    Route::middleware('api')->post('/broadcast/block',
            'BroadcastController@postBlock');// body: ['block': BlockHash, 'peer' => 'source host']
    Route::middleware('api')->get('/transactions/{hash}', 'TransactionController@getTransaction'); // Transaction
    
    // Miner
    
    Route::middleware('api')->get('/miner/job/{miner_address}',
            'MinerController@getJob'); // Block, without nonce and timestamp
    Route::middleware('api')->post('/miner/job',
            'MinerController@postJob'); // body: ['block': Block, with nonce and timestamp]
    Route::middleware('api')->get('/miner/last-block-hash',
            'MinerController@getLastBlockHash'); // ['hash': Last block's hash]
    
    // Wallet & Faucet
    
    Route::middleware(['api'])->get('/balance/{address}',
            'BalanceController@getBalance'); // [confirmed: int, unconfirmed: int, txs: int]
    Route::middleware(['api'])->post('/transaction', 'TransactionController@postTransaction'); // Body: ['transaction': Transaction]
    Route::middleware(['api'])->get('/transactions/address/{address}', 'TransactionController@getTransactionsForAddress'); // Transaction[]
    
    // Explorer
    
    Route::middleware('api')->get('/blocks/last/{limit}', 'BlockController@getLastBlocks'); // Block[]
    Route::middleware('api')->get('/blocks/{hash}', 'BlockController@getBlockInfo'); // Block + Transactions
}
