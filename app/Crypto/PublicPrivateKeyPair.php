<?php

namespace App\Crypto;

use Elliptic\EC;

class PublicPrivateKeyPair
{
  /**
   * @var EC
   */
  private $ec;
  /**
   * @var EC\KeyPair
   */
  private $private;
  
  /**
   * @var \Elliptic\Curve\ShortCurve\Point
   */
  private $public;
  
  /**
   * PrivateKey constructor.
   * @param EC $ec
   * @param EC\KeyPair $private
   */
  private function __construct(EC $ec, EC\KeyPair $private)
  {
    $this->ec = $ec;
    $this->private = $private;
    $this->public = $private->getPublic();
  }

  public static function fromPrivateKey($privateKeyHex)
  {
    $ec = new EC('secp256k1');
    return new self($ec, $ec->keyFromPrivate($privateKeyHex, 'hex'));
  }
  
  public static function generate(){
    $ec = new EC('secp256k1');
    $private = $ec->genKeyPair();
    return new self($ec, $private);
  }
  
  public function getPrivateKey()
  {
    return $this->private->getPrivate('hex');
  }
  
  public function getPublicKey()
  {
    return $this->public->getX()->toString('hex') . $this->public->getY()->toString('hex');
  }
  
  public function getCompressedPublicKey(){
    return $this->public->getX()->toString('hex') . ($this->public->getY()->isEven() ? '0' : '1');
  }
  
  public function getAddress(){
    return hash('ripemd160', $this->getCompressedPublicKey());
  }
  
  public function sign($hash){
      $signature = $this->private->sign($hash, 'hex', ['canonical' => true]);
      return $signature->r->toString('hex') . $signature->s->toString('hex') . str_pad(dechex($signature->recoveryParam),
                      2, '0', STR_PAD_LEFT);
  }
}