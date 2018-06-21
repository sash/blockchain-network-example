<?php

namespace App\Console\Commands\Examples;

use App\Crypto\EthereumAddress;
use App\Crypto\EthereumSign;
use Illuminate\Console\Command;

use Elliptic\EC;
use kornrunner\Keccak;

/**
 * Class ECC
 * @package App\Console\Commands
 *
 * 5.  Ethereum Signature Creator
 * 6.  Ethereum Signature to Address
 * 7.  Ethereum Signature Verifier
 *
 */
class ECC extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blockchain:ecc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exercises: Blockchain Cryptography. 5.  Ethereum Signature Creator, 6.  Ethereum Signature to Address, 7.  Ethereum Signature Verifier';

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
      $message = 'exercise-cryptography';
      $privateKey = '97ddae0f3a25b92268175400149d65d6887b9cefaf28ea2c078e05cdc15a3c0a';
      
      $signer = new EthereumSign($privateKey, $message);
      $json['signature'] = $signer->getSignature();
      $json['r'] = $signer->getSignatureR();
      $json['s'] = $signer->getSignatureS();
      $json['v'] = '0x1';
      
      echo json_encode($json);
  
  
      echo "\n\n";
      
      $json = json_decode('{"signature":"0xacd0acd4eabd1bec05393b33b4018fa38b69eba8f16ac3d60eec9f4d2abc127e3c92939e680b91b094242af80fce6f217a34197a69d35edaf616cb0c3da4265b01","v":"0x1","r":"0xacd0acd4eabd1bec05393b33b4018fa38b69eba8f16ac3d60eec9f4d2abc127e","s":"0x3c92939e680b91b094242af80fce6f217a34197a69d35edaf616cb0c3da4265b"}', true);
  
      $signature = $json['signature'];
  
      $address = EthereumAddress::fromSignature($signature, $message)->getAddress();
      
      echo $address;
  
      echo "\n\n";
  
      $address =
      '0xa44f70834a711F0DF388ab016465f2eEb255dEd0';
$signature =
'0xacd0acd4eabd1bec05393b33b4018fa38b69eba8f16ac3d60eec9f4d2abc127e3c92939e680b91b094242af80fce6f217a34197a69d35edaf616cb0c3da4265b01';
$message = 'exercise-cryptography';
      if ($this->verifySignature($message, $signature, $address)){
        echo 'Valid';
      } else {
        echo 'Invalid';
      }
  
      echo "\n\n";
  
      $address =
        '0xa44f70834a711F0DF388ab016465f2eEb255dEd0';
      $signature =
        '0x5550acd4eabd1bec05393b33b4018fa38b69eba8f16ac3d60eec9f4d2abc127e3c92939e680b91b094242af80fce6f217a34197a69d35edaf616cb0c3da4265b01';
      $message = 'exercise-cryptography';
      if ($this->verifySignature($message, $signature, $address)) {
        echo 'Valid';
      } else {
        echo 'Invalid';
      }
  
    }
  
  function verifySignature($message, $signature, $address)
  {
    return strtolower($address) == (EthereumAddress::fromSignature($signature, $message)->getAddress());
  }
  
  
}
