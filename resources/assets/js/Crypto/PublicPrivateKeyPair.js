import {ec as EC} from 'elliptic';

import * as RIPEMD160 from 'ripemd160';

export default class PublicPrivateKeyPair {
    constructor(ec, privateKey) {
        this.ec = ec
        this.privateKey = privateKey
        this.publicKey = privateKey.getPublic();
    }

    static generate() {
        const ec = new EC('secp256k1');
        const privateKey = ec.genKeyPair();
        return new this (ec, privateKey);
    }

    static fromPrivate(privateKeyHex){
        const ec = new EC('secp256k1');
        return new this(ec, ec.keyFromPrivate(privateKeyHex, 'hex'));

    }

    getPrivateKey() {
        return this.privateKey.getPrivate('hex');
    }

    getCompressedPublicKey() {
        return this.publicKey.getX().toString('hex') + (this.publicKey.getY().isEven() ? '0' : '1');
    }

    getAddress() {
        return new RIPEMD160().update(this.getCompressedPublicKey()).digest('hex')
    }

    sign(hash) {
        const signature = this.privateKey.sign(hash, 'hex', {canonical: true});
        // return signature.r.toString('hex').padStart(64, '0') + signature.s.toString('hex').padStart(64, '0') + signature.recoveryParam.toString(16).padStart(2, "0");
        return signature.r.toString('hex') + signature.s.toString('hex') + signature.recoveryParam.toString(16).padStart(2, "0");
    }
}
