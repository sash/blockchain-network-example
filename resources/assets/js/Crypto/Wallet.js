import PublicPrivateKeyPair from './PublicPrivateKeyPair'

import * as HDKey from 'hdkey'
import * as bip39 from 'bip39'
import * as CryptoJS from 'crypto-js';

var walletInstance = null;

export default class Wallet{
    constructor(masterKey, entropy){
        this.masterKey = masterKey
        this.entropy = entropy
    }

    // Singleton (if unlocked)
    static getInstatance() {
        return walletInstance;
    }

    // Actions
    static restore(mnemonic, password, repeatPassword){
        if (password !== repeatPassword){
            throw new Error('Password did not match');
        }
        const wallet = this.fromMnemonic(mnemonic);
        wallet.toStorage(password)
        walletInstance = wallet;
        return true;
    }
    static unlock(password){
        if (this.hasStorage()){
            walletInstance = this.fromStorage(password);
            return true;
        } else {
            return false;
        }
    }
    static lock(){
        walletInstance = null;
    }
    static forget(){
        this.lock();
        this.clearStorage();
    }
    static new(password, repeatPassword){
        if (password !== repeatPassword) {
            throw new Error('Password did not match');
        }

        walletInstance = this.generate();
        walletInstance.toStorage(password);
        return true;
    }

    // Factories
    static fromMnemonic(mnemonic) {
        const seed = bip39.mnemonicToSeed(mnemonic);
        const masterKey = HDKey.fromMasterSeed(seed);
        return new this(masterKey, bip39.mnemonicToEntropy(mnemonic));
    }
    static generate(){
        const mnemonic = bip39.generateMnemonic()
        const entropy = bip39.mnemonicToEntropy(mnemonic)
        const seed = bip39.mnemonicToSeed(mnemonic);
        const masterKey = HDKey.fromMasterSeed(seed);
        return new this(masterKey, entropy)
    }
    static hasStorage(){
        const encr = window.localStorage.getItem("encryptedEntropy");
        const checksum = window.localStorage.getItem("encryptedEntropyChecksum");
        if (!encr || !checksum) {
            return false;
        }
        return true;
    }
    static fromStorage(password){
        const encr = window.localStorage.getItem("encryptedEntropy");
        const checksum = window.localStorage.getItem("encryptedEntropyChecksum");
        if (!encr || !checksum){
            throw new Error("No wallet stored");
        }
        const entropy = this._decrypt(encr, password);
        if (checksum !== CryptoJS.SHA256(entropy).toString()){
            throw new Error("Invalid password");
        }
        const mnemonic = bip39.entropyToMnemonic(entropy);
        return this.fromMnemonic(mnemonic);
    }
    static clearStorage(){
        window.localStorage.removeItem("encryptedEntropy")
        window.localStorage.removeItem("encryptedEntropyChecksum")
    }
    static _decrypt(message, password){
        var bytes = CryptoJS.AES.decrypt(message, password);
        return bytes.toString(CryptoJS.enc.Utf8);
    }
    static _encrypt(message, password){
        return CryptoJS.AES.encrypt(message, password).toString();
    }

    // Members
    account(number){
        const childKey = this.masterKey.derive("m/0/" + 0x100.toString(10) + "'/0'/0'/0/" + number);
        return PublicPrivateKeyPair.fromPrivate(childKey.privateKey.toString('hex'));
    }
    toStorage(password){
        const encr = Wallet._encrypt(this.entropy, password);
        const hash = CryptoJS.SHA256(this.entropy).toString();
        window.localStorage.setItem("encryptedEntropy", encr);
        window.localStorage.setItem("encryptedEntropyChecksum", hash);
    }
    getMnemonic(){
        return bip39.entropyToMnemonic(this.entropy);
    }
}