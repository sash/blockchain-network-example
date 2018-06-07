<?php

namespace App\Crypto;

use Elliptic\EC;
use kornrunner\Keccak;

class EthereumSign
{
  private $signatureS;
  private $signatureR;
  private $hash;
  private $signature;
  private $privateKey;
  private $message;
  
  /**
   * EthereumSign constructor.
   * @param $privateKey
   * @param $message
   */
  public function __construct($privateKey, $message) //, $etheriumFormat = false
  {
    $this->privateKey = $privateKey;
    $this->message = $message;
//    if ($etheriumFormat){
//      $msglen = strlen($message);
//      $this->message = "\x19Ethereum Signed Message:\n{$msglen}{$message}";
//    }
  }
  
  /**
   * @return mixed
   * @throws \Exception
   */
  public function getSignature()
  {
    $this->assertSignature();
    return $this->signature;
  }
  
  public function getSignatureR(){
    $this->assertSignature();
    return $this->signatureR;
  }
  
  public function getSignatureS()
  {
    $this->assertSignature();
    return $this->signatureS;
  }
  
  public function getHash()
  {
    $this->assertHash();
    return $this->hash;
  }
  
  private function assertHash()
  {
    if (!isset($this->hash)) {
      $this->hash = Keccak::hash($this->message, 256);
    }
  }
  
  
  
  /**
   * @throws \Exception
   */
  private function assertSignature(){
    if (!isset($this->signature)){
      $this->sign();
    }
  }
  
  
  
  /**
   * @param $message
   * @param $privateKey
   * @return array
   * @throws \Exception
   */
  private function sign()
  {
    $crypto = new EC('secp256k1');
    $key = $crypto->keyFromPrivate($this->privateKey, 'hex');
    $signature = $key->sign($this->getHash(), 'hex', ['canonical' => true]);
    
    $this->signature = '0x' . $signature->r->toString('hex') . $signature->s->toString('hex') . '01';
    $this->signatureR = $signature->r->toString('hex');
    $this->signatureS = $signature->s->toString('hex');
  }
  
}