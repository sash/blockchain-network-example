This repo contains Alexander Alexie's homework from lecture

# Project structure
Docker will be used to launch all the different components of the project - nodes, wallets, workers and so on.

The Node will be implemented in PHP using Laravel. It will be REST API that the other components can use. It will communicate with the other nodes

The Miner will be implemented in nodejs using express and it will communicate with a node's REST API to get work and register results

The Wallet will be impoemented in PHP using Laravel. For simplicity the same laravel project will be used (as the other PHP implementations). Different instancess will be launched and different endpoints will be used. The private key will be passed via format similar to mypasswallet.

The Faucet will be implemented in nodejs using express and will communicate with a node.

The Blockchain explorer will be implemented in PHP using Laravel. For simplicity the same laravel project will be used (as the other PHP implementations). It will run on separate endpoints and will.


# Node
## Data Structure
* Transaction
* Block

## Validators
* ValidateTransaction
* ValidateBlock
* ValidateBlockChain



## REST



## Instructions


Docker + Docker compose are required in order to build the dev enviroment. To install docker go to https://www.docker.com/get-docker

* run `docker-compose run node1 composer install` to setup PHP dependencies
* run `docker-compose up -d` to bring the conteiners online
* run `docker-compose exec app php artisan` to see available solutions (they are in the blockchain namespace). Run the examples using `docker-compose exec app php artisan blockchain:[example-name]`
```
blockchain
  blockchain:ecc        Exercises: Blockchain Cryptography. 5.  Ethereum Signature Creator, 6.  Ethereum Signature to Address, 7.  Ethereum Signature Verifier
  blockchain:ethereum   Exercises sign verify ethereum message
  blockchain:hash       Exercises: Blockchain Cryptography. 1) Calculate Hashes
  blockchain:hmac       Exercises: Blockchain Cryptography. 2) Calculate HMAC
  blockchain:scrypt     Exercises: Blockchain Cryptography. 3) Derive Key by Password using SCrypt
  blockchain:symmetric  Exercises: Blockchain Cryptography. 4) Symmetric Encryption / Decryption (AES + SCrypt + HMAC)
```
* run `docker-compose exec app yarn install` to setup node dependencies
* Exercises: Blockchain Cryptography 8. Bitcoin Address Generator (C# Edition) is in c#/BitcoinAddressGenerator.cs
* Exercises: Blockchain Cryptography 9. Bitcoin Address Generator (JS Edition) is in `docker-compose exec app node node/bitcoin-address-generator.js`
* Exercises: Blockchain Cryptography 10. Private Key to Bitcoin Address is in `docker-compose exec app node node/private-key-to-bitcoin-address.js`
* Exercises: Blockchain Cryptography 11. Asymmetric Encryption / Decryption is in `docker-compose exec app node node/asymmetric-encryption-decryption.js`

Homework for Lecture 5 (Consensus Algorithms)
* `docker-compose exec app php artisan blockchain:private-to-address`
* "Exercises: Sign and Verify Transaction in JavaScript" is in `docker-compose exec app node node/sign-and-verify-transactions.js`


## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT). The rest of the code is also licensed under [MIT license](https://opensource.org/licenses/MIT)
