<?php

namespace App\Console\Commands\Examples;

use App\Crypto\EthereumAddress;
use App\Crypto\EthereumSign;
use Illuminate\Console\Command;

class Ethereum extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blockchain:ethereum';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exercises sign verify ethereum message';

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
        $message = 'Message for signing';
        $privateKey = '97ddae0f3a25b92268175400149d65d6887b9cefaf28ea2c078e05cdc15a3c0a';
        $signer = new EthereumSign($privateKey, $message, true);
        $res = [];
        $res['sig'] = $signer->getSignature();
        $res['msg'] = $message;
        $res['version'] = 1;
        $res['address'] = EthereumAddress::fromPrivateKey($privateKey)->getAddress();
        
        echo json_encode($res);
  
      echo "\n\n";
        
        $this->verifyJsonSignature(json_decode('{
  "address": "0xa44f70834a711f0df388ab016465f2eeb255ded0",
  "msg": "Message for signing",
  "sig": "0x6f0156091cbe912f2d5d1215cc3cd81c0963c8839b93af60e0921b61a19c54300c71006dd93f3508c432daca21db0095f4b16542782b7986f48a5d0ae3c583d401",
  "version": "1"
}', true));
  
      $this->verifyJsonSignature(json_decode('{
  "address": "0xa44f70834a711f0df388ab016465f2eeb255ded0",
  "msg": "Tampered message",
  "sig": "0x6f0156091cbe912f2d5d1215cc3cd81c0963c8839b93af60e0921b61a19c54300c71006dd93f3508c432daca21db0095f4b16542782b7986f48a5d0ae3c583d401",
  "version": "1"
}', true));
    }
  
  
    private function verifyJsonSignature($json){
      if ($this->verifySignature($json['msg'], $json['sig'], $json['address'])){
        echo "Valid\n";
      } else {
        echo "Invalid\n";
      }
  
      echo "\n\n";
    }
  private function verifySignature($message, $signature, $address)
  {
    return strtolower($address) == (EthereumAddress::fromSignature($signature, $message)->getAddress());
  }
  
}
