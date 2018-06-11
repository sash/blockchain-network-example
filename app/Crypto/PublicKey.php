<?php

namespace App\Crypto;

use App\NodeTransaction;
use Elliptic\Curve\BaseCurve\Point;
use Elliptic\EC;
use kornrunner\Keccak;

class PublicKey
{
    private $publicKey;
    
    /**
     * @param Point $publicKey
     */
    private function __construct($publicKey)
    {
        $this->publicKey = $publicKey;
    }
    
    static function fromPrivateKey($privateKey)
    {
        $ec = new EC('secp256k1');
        return new self($ec->keyFromPrivate($privateKey, 'hex')->getPublic());
    }
    
    /**
     * @param $signature
     * @param NodeTransaction $transaction
     * @return PublicKey
     * @throws \Exception
     */
    static function fromSignature(NodeTransaction $transaction, $signature = null)
    {
        $signature = $transaction->signature ?: $signature;
    
        $ec = new EC('secp256k1');
        $hash = (new TransactionHasher())->getHash($transaction);
        $sign = [
                "r" => substr($signature, 0, 64),
                "s" => substr($signature, 64, 64)
        ];
        $recid = ord(hex2bin(substr($signature, 128, 2)));
        if ($recid != ($recid & 1)) {
            throw new \Exception('Invalid recovery ID:' . $recid);
        }
        /**
         * @var \Elliptic\Curve\ShortCurve\Point $pubkey
         */
        $pubkey = $ec->recoverPubKey($hash, $sign, $recid);
        
        return new self($pubkey);
    }
    
    public function getAddress()
    {
        return hash('ripemd160', hex2bin($this->publicKey->encode("hex")));
    }
}