require('./bootstrap');

import PublicPrivateKeyPair from './Crypto/PublicPrivateKeyPair'

import * as HDKey from 'hdkey'
import * as bip39 from 'bip39'


var mnemonic = bip39.generateMnemonic()
console.log(mnemonic);

const seed = bip39.mnemonicToSeed(mnemonic);
const masterKey = HDKey.fromMasterSeed(seed);

console.log(masterKey);

console.log(masterKey.toJSON())
console.log('p', masterKey.privateKey.toString('hex'))
console.log('ex', masterKey.privateExtendedKey.toString('hex'))

console.log("m/0/" + 0x100.toString(10) + "'/0'/0'/0/0");

const childKey = masterKey.derive("m/0/" + 0x100.toString(10) + "'/0'/0'/0/0");

console.log(childKey.privateKey.toString('hex'));

const keys = PublicPrivateKeyPair.fromPrivate(masterKey.privateKey.toString('hex'));

console.log('priv', keys.getPrivateKey());
console.log(keys.getCompressedPublicKey());
console.log(keys.getAddress());