const ecies = require('bitcore-ecies');
const bitcore = require('bitcore-lib');



let alicePair = new bitcore.PrivateKey()
let bobPair = new bitcore.PrivateKey()


let alice = ecies();
alice.privateKey(alicePair).publicKey(bobPair.publicKey);


var message = 'some secret message';
var encrypted = alice.encrypt(message);

console.log(encrypted.toString());


let bob = ecies();
bob.privateKey(bobPair).publicKey(alicePair.publicKey);


let decripted = bob.decrypt(encrypted);
console.log(decripted.toString());