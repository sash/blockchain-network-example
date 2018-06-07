This repo contains Alexander Alexie's homework from lecture

## Instructions


Docker + Docker compose are required in order to build the dev enviroment. To install docker go to https://www.docker.com/get-docker

* run `docker-compose run app composer install` to setup PHP dependencies
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
* run `docker-compose exec app npm install` to see setup PHP dependencies
* Exercises: Blockchain Cryptography 8. Bitcoin Address Generator (C# Edition) is in c#/BitcoinAddressGenerator.cs
* Exercises: Blockchain Cryptography 9. Bitcoin Address Generator (JS Edition) is in `docker-compose exec app node node/bitcoin-address-generator.js`
* Exercises: Blockchain Cryptography 10. Private Key to Bitcoin Address is in `docker-compose exec app node node/private-key-to-bitcoin-address.js`
* Exercises: Blockchain Cryptography 11. Asymmetric Encryption / Decryption is in `docker-compose exec app node node/asymmetric-encryption-decryption.js`

Homework for Lecture 5 (Consensus Algorithms)
* `docker-compose exec app php artisan blockchain:private-to-address`
* "Exercises: Sign and Verify Transaction in JavaScript" is in `docker-compose exec app node node/sign-and-verify-transactions.js`


## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT). The rest of the code is also licensed under [MIT license](https://opensource.org/licenses/MIT)
