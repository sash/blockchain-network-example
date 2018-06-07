const bitcoin = require('bitcoinjs-lib');

let pair = bitcoin.ECPair.fromWIF('5HueCGU8rMjxEXxiPuD5BDku4MkFqeZyd4dZ1jvhTVqvbTLvyTJ');

console.log(pair.getAddress())