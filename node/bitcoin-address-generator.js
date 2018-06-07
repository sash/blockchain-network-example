const bitcoin = require('bitcoinjs-lib');
let keyPair = bitcoin.ECPair.makeRandom();

let privateKey = keyPair.toWIF();
console.log(`Private Key: ${privateKey}`);

let publicKey = keyPair.getPublicKeyBuffer().toString('hex');
console.log(`Public Key: ${publicKey}`);

let address = keyPair.getAddress();
console.log(`Address: ${address}`);