<?php

namespace App\Console\Commands\Examples;

use Illuminate\Console\Command;

class Symmetric extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blockchain:symmetric';

    /**
     * The console command description.
     *
     * @var string
     */
  protected $description = 'Exercises: Blockchain Cryptography. 4) Symmetric Encryption / Decryption (AES + SCrypt + HMAC)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      $password = 'p@$$w0rd~3';
      $message = 'exercise-cryptography';
  
      $json = $this->encode($password, $message);
      
      echo json_encode($json);
      
      // Decode
    
      echo $this->decode($json, $password);
    }
  
  /**
   * @param $password
   * @param $message
   * @return array
   * @throws \Exception
   */
  private function encode($password, $message): array
  {
    
//    $salt = bin2hex(random_bytes(32));
  
  $salt = '7b07a2977a473e84fc30d463a2333bcfea6cb3400b16bec4e17fe981c925';
    list($enctyprtionKey, $hmacKey) = $this->deriveKeys($password, $salt);
//    $IV = random_bytes(16);
      $IV = '433e0d8557a800a40c1d3b54f6636ff5';
    
    $crypt = new \phpseclib\Crypt\Twofish(\phpseclib\Crypt\Twofish::MODE_CBC);
    $crypt->setKey($enctyprtionKey);
    
    $crypt->setIV($IV);
    $res = $crypt->encrypt($message);
    $hmac = hash_hmac('sha256', $res, $hmacKey);
    $json = [
      'scrypt'  => [
        "dklen" => 64,
        "salt"  => $salt,
        "n"     => 16384,
        "r"     => 16,
        "p"     => 1
      ],
      'twofish' => bin2hex($res),
      'iv'      => bin2hex($IV),
      'mac'     => $hmac
    ];
    return $json;
  }
  
  private function decode($json, $password){
    $salt = $json['scrypt']['salt'];
  
  
    list($enctyprtionKey, $hmacKey) = $this->deriveKeys($password, $salt);
  
    $expectedHmac = hash_hmac('sha256', hex2bin($json['twofish']), $hmacKey);
    if ($expectedHmac != $json['mac']){
      throw new \Exception("MAC did not match. You've got the wrong password");
    }
    
  
    $crypt = new \phpseclib\Crypt\Twofish(\phpseclib\Crypt\Twofish::MODE_CBC);
    $crypt->setKey($enctyprtionKey);

    $crypt->setIV(hex2bin($json['iv']));
    $res = $crypt->decrypt(hex2bin($json['twofish']));
    return $res;
  }
  
  /**
   * @param $password
   * @param $salt
   * @return array
   */
  private function deriveKeys($password, $salt): array
  {
    $key = scrypt($password, $salt,
      16384, 16, 1, 64);
    
    $enctyprtionKey = substr($key, 0, 64);
    $hmacKey = substr($key, 64, 64);
    return array($enctyprtionKey, $hmacKey);
  }
}
