const ethers = require('ethers');


function createWalletFromPrivateKey(privateKey){
    return new ethers.Wallet(privateKey)
}

let privateKey = "0x495d5c34c912291807c25d5e8300d20b749f6be44a178d5c50f167d495f3315a";
console.log(createWalletFromPrivateKey(privateKey))


function generateRandomWallet() {
    return ethers.Wallet.createRandom();
}

console.log(generateRandomWallet())


async function saveWalletAsJson(wallet, password) {
    return wallet.encrypt(password)
}

(
    async () => {
        let wallet = createWalletFromPrivateKey(privateKey)
        let password = 'p@$$word';
        let json = await saveWalletAsJson(wallet, password);
        console.log(json)
    }
)();


async function decryptWallet(json, password) {
    return ethers.Wallet.fromEncryptedWallet(json, password);
}

(
    async () => {
        let wallet = createWalletFromPrivateKey(privateKey)
        let password = 'p@$$word';
        let json = await saveWalletAsJson(wallet, password);


        let walletDecrypted = await decryptWallet(json, password);
        console.log(walletDecrypted)
    }
)();


function signTransaction(wallet, toAddress, value) {
    let transaction = {
        nonce: 0,
        gasLimit: 21000,
        gasPrice: ethers.utils.bigNumberify("2000000000"),
        to: toAddress,
        value: ethers.utils.parseEther(value),
    }
    return wallet.sign(transaction);
}

let signedTransaction = signTransaction(createWalletFromPrivateKey(privateKey), "0x7725f560672A512e0d6aDFE7a761F0DbD8336aA7", "1.0")

console.log('Signed transaction: \n' + signedTransaction)

