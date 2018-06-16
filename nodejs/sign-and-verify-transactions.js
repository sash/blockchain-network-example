const CryptoJS = require('crypto-js');
const EC = require('elliptic').ec;
const secp256k1 = new EC('secp256k1');

const signData = (data, privateKey) => {
    let keyPair = secp256k1.keyFromPrivate(privateKey);
    let signature = keyPair.sign(data);
    return [signature.r.toString(16), signature.s.toString(16)];
}

const compressedPublicKeyFromPrivate = (privateKey) => {
    let keyPair = secp256k1.keyFromPrivate(privateKey);
    let publicKey =  keyPair.getPublic()
    var x = publicKey.getX();
    var y = publicKey.getY();
    return x.toString('hex') + (y.isOdd() ? 1 : 0);
}

const decompressPublicKey = (compressedPublicKey) => {
    let publicKeyX = compressedPublicKey.substring(0, 64)
    ;
    let publicKeyYIsOdd = parseInt(compressedPublicKey.substring(64));
    let publicKey = secp256k1.curve.pointFromX(publicKeyX, publicKeyYIsOdd);

    return publicKey;
}

const verifySignature = (data, publicKeyCompressed, signature) => {
    let publicKey = decompressPublicKey(publicKeyCompressed);
    let keyPair = secp256k1.keyPair({pub: publicKey});
    let result = keyPair.verify(data, {r: signature[0], s: signature[1]});

    return result;
}

class Transaction {
    constructor(from, to, value, fee, dateCreated, data, senderPublicKey) {
        this.fromAddressHex = from;
        this.toAddressHex = from;
        this.value = value;
        this.fee = fee;
        this.dateCreated = dateCreated;
        this.data = data;
        this.senderPublicKeyCompressedHex = senderPublicKey;

        this.calculateHash();
    }

    calculateHash(){
        let json = JSON.stringify(this)
        this.transactionHash = CryptoJS.SHA256(json).toString()
    }
    sign(privateKey){
        if (compressedPublicKeyFromPrivate(privateKey) !== this.senderPublicKeyCompressedHex){
            throw new Error('Transaction can only be signed with the senders private key!');
        }
        this.senderSignature = signData(this.transactionHash, privateKey);
    }
    verify(){
        return verifySignature(this.transactionHash, this.senderPublicKeyCompressedHex, this.senderSignature)
    }
}

let transaction = new Transaction(
    "c3293572dbe6ebc60de4a20ed0e21446cae66b17",
    "f51362b7351ef62253a227a77751ad9b2302f911",
    25000,
    10,
    "2018-02-10T17:53:48.972Z",
    "Send to Bob",
    "c74a8458cd7a7e48f4b7ae6f4ae9f56c5c88c0f03e7c59cb4132b9d9d1600bba1"
);

transaction.sign("7e4670ae70c98d24f3662c172dc510a085578b9ccc717e6c2f4e547edd960a34");

console.log(transaction.senderSignature); // The signature does not match the expected once, because I named my transaction fields more descriptively then the example, and those go in the hash. In reality we would compact and only sign the data (not the json)

console.log(transaction.verify());

