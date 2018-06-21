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
    
    /**
     * @param $publicKeyHex
     * @return PublicKey
     */
    static function fromHex($publicKeyHex){
        $ec = new EC('secp256k1');
    
        return new self($ec->keyFromPublic($publicKeyHex, 'hex'));
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
    static function fromSignature(NodeTransaction $transaction, $signature = null, $hash = null)
    {
        $signature = $transaction->signature ?: $signature;
    
        $ec = new EC('secp256k1');
        $hash = $hash ?: (new TransactionHasher(new TransactionSerializer()))->getHash($transaction);
        $sign = [
                "r" => substr($signature, 0, 64),
                "s" => substr($signature, 64, 64)
        ];
        $recid = hexdec(substr($signature, 128, 2));
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
        return hash('ripemd160', $this->getCompressedPublicKey());
    }
    
    public function getCompressedPublicKey()
    {
        return $this->publicKey->getX()->toString('hex') . ($this->publicKey->getY()->isEven() ? '0' : '1');
    }
}