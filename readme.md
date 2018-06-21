[![Build Status](https://travis-ci.org/sash/blockchain-network-example.svg?branch=develop)](https://travis-ci.org/sash/blockchain-network-example)

# Initial funds owners
| Address                                  | Private Key                                                      |
| ---------------------------------------- | -----------------------------------------------------------------|
| be9c053812ca0cf8ae40aab3047f1b17e586765d | 0f9d3070204642bc8eb07b00a99ef38eebfec965733a3f70548ce99484fdfd99 |
| c06e8b1d745f50658be0a6e4bd6b01c94878a923 | e5fcb644cb5ff2a34d8d479b2fc775c6e4f242ebd8f4eb146bf3985d968c67a5 |
| 9a0bc19436ff653a7c631edc82451a684bccbbb2 | 1827f2551a5e6c64f4a601c569c3a092c8a1dd770246947ecc8d6f01b29db2db |
| b379a0f6378b612a46a346e8136ba3b9fb324218 | b3cf4c12b7e41b138ce19af734e7f3856a58858ca1430fb0f0c086b4f644c476 |
| 626b5ce05e2b40812cf283fc45434e799f036d9c | 05770798da086eab3d7e665e883d62003018d02f4021d2b9598f3ff9e11b2cc0 |
| f86d8b68d81bd1bb2637e4874c31c1d5bd3a0169 | 98acc7b63049233d873c2dda03c7c29ead53a816ef463225dd9d72da9d69c884 |
| b0238c61a3bfe54bd7ae95aeaea25a3a942759e4 | 906bad6aa7fc42f38c3dc6bc51729e645fe3b8b1221323ffe35e4fa1029792f8 |
| a163ad1c02203518eeae23f6348d8c1dc00dac25 | e3c2b302c54725f3f6029ed6829e7b9f8c1a1e4aff5c4ced054cf16cef7f311d |
| 6b2bc3ffcda1f096a3ac08fa38352505f57e543e | 42367b96408ddb7c5f5d79163add487dde661ad474085b662bbddf6edebe92ec |
| 004ca2dd10fcf53ad30631e7d323aa80c9ecf317 | 600fea4a214cadb607e34ed0bb091297864cc12162f1e6d6f67a4c5efac06e05 |



# Scenarios

## Install
`docker-compose run node1 php artisan migrate:fresh ; docker-compose run node1 php artisan db:seed`
`docker-compose run node2 php artisan migrate:fresh ; docker-compose run node2 php artisan db:seed`

## Bootstrap
`docker-compose exec node1 php artisan node:bootstrap`
`docker-compose exec node2 php artisan node:bootstrap`

## Add more miners
`docker-compose scale miner1=5 miner2=5`

## Business as usual
1. Make a new wallet http://localhost:5003
2.


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
* run `docker-compose exec node1 yarn install` to setup node dependencies
* Exercises: Blockchain Cryptography 8. Bitcoin Address Generator (C# Edition) is in c#/BitcoinAddressGenerator.cs
* Exercises: Blockchain Cryptography 9. Bitcoin Address Generator (JS Edition) is in `docker-compose exec app node node/bitcoin-address-generator.js`
* Exercises: Blockchain Cryptography 10. Private Key to Bitcoin Address is in `docker-compose exec app node node/private-key-to-bitcoin-address.js`
* Exercises: Blockchain Cryptography 11. Asymmetric Encryption / Decryption is in `docker-compose exec app node node/asymmetric-encryption-decryption.js`

Homework for Lecture 5 (Consensus Algorithms)
* `docker-compose exec app php artisan blockchain:private-to-address`
* "Exercises: Sign and Verify Transaction in JavaScript" is in `docker-compose exec app node node/sign-and-verify-transactions.js`


## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT). The rest of the code is also licensed under [MIT license](https://opensource.org/licenses/MIT)
