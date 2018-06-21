<?php
/**
 * Created by PhpStorm.
 * User: sash
 * Date: 21.6.2018
 * Time: 18:15
 */

namespace Tests\Unit\Crypto;

use App\Crypto\TransactionHasher;
use App\Crypto\TransactionSerializer;
use App\Http\Resources\NodeTransactionResource;
use App\NodeTransaction;
use PHPUnit\Framework\TestCase;

class TransactionHasherTest extends TestCase
{
    
    public function testGetHash()
    {
        $transactionJson = json_decode('{"senderAddress":"0000000000000000000000000000000000000000","senderSequence":0,"receiverAddress":"be9c053812ca0cf8ae40aab3047f1b17e586765d","value":10000000000,"fee":0,"data":"","timestamp":1529067174,"hash":"ae4b979d0059bd5af75bae39bf56508a5d2da0a60bb8394b6a294425c820b673","signature":"0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000"}', true);
        $transaction = new NodeTransaction($transactionJson);
        $hasher = new TransactionHasher(new TransactionSerializer());
        $this->assertEquals('ae4b979d0059bd5af75bae39bf56508a5d2da0a60bb8394b6a294425c820b673', $hasher->getHash($transaction));
    }
}
