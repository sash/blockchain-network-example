<?php

namespace App\Crypto;

use Elliptic\EC;
use kornrunner\Keccak;

class EthereumAddress
{
  private $publicKey;
  
  /**
   * EthereumAddress constructor.
   * @param $publicKey
   */
  public function __construct($publicKey)
  {
    $this->publicKey = $publicKey;
  }
  
  static function fromPrivateKey($privateKey)
  {
    $ec = new EC('secp256k1');
    return new self($ec->keyFromPrivate($privateKey, 'hex')->getPublic());
  }
  
  static function fromSignature($signature, $message){
  
    $ec = new EC('secp256k1');
    $hash = Keccak::hash($message, 256);
    $sign = [
      "r" => substr($signature, 2, 64),
      "s" => substr($signature, 66, 64)
    ];
    $recid = ord(hex2bin(substr($signature, 130, 2)));
    if ($recid != ($recid & 1)) {
      return false;
    }
    $pubkey = $ec->recoverPubKey($hash, $sign, $recid);
    
    return new self($pubkey);
  }
  
  public function getAddress()
  {
    return "0x" . substr(Keccak::hash(substr(hex2bin($this->publicKey->encode("hex")), 1), 256), 24);
  }
}