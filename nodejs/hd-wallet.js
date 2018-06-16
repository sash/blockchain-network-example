const ethers = require('ethers');


let mnemonic = 'upset fuel enhance depart portion hope core animal innocent will athlete snack';

function restoreHDNode(mnemonic){
    return ethers.HDNode.fromMnemonic(mnemonic)
}

console.log(restoreHDNode(mnemonic))

function restoreHDWallet(mnemonic){
    return ethers.Wallet.fromMnemonic(mnemonic);
}

console.log(restoreHDWallet(mnemonic))

function generateMnemonic(){
    let randomEntropyBytes = ethers.utils.randomBytes(16);
    return ethers.HDNode.entropyToMnemonic(randomEntropyBytes);
}

function generateRandomHDNode(){
    let mnemonic = generateMnemonic();
    return restoreHDNode(mnemonic);
}

console.log(generateRandomHDNode());

function generateRandomWallet(){
    return ethers.Wallet.createRandom();
}

console.log(generateRandomWallet())

async function saveWalletAsJson(wallet, password){
    return wallet.encrypt(password)
}

(
    async() => {
        let wallet = ethers.Wallet.createRandom()
        let password = 'p@$$word';
        let json = await saveWalletAsJson(wallet, password);
        console.log(json)
    }
)();

async function decryptWallet(json, password){
    return ethers.Wallet.fromEncryptedWallet(json, password);
}

(
    async () => {
        let wallet = ethers.Wallet.createRandom()
        let password = 'p@$$word';
        let json = await saveWalletAsJson(wallet, password);


        let walletDecrypted = await decryptWallet(json, password);
        console.log(walletDecrypted)
    }
)();


function deriveFiveWalletsFromHDNode(mnemonic, derivationPath){
    let wallets = [];

    for (let i = 0; i < 5; i++){
        let HDNode = ethers.HDNode.fromMnemonic(mnemonic).derivePath(derivationPath + i)
        console.log(HDNode);

        let wallet = new ethers.Wallet(HDNode.privateKey)
        wallets.push(wallet);
    }
    return wallets;
}

console.log(deriveFiveWalletsFromHDNode(mnemonic, "m/44'/60'/0'/0"));

function signTransaction(wallet, toAddress, value){
    let transaction = {
        nonce: 0,
        gasLimit: 21000,
        gasPrice: ethers.utils.bigNumberify("2000000000"),
        to: toAddress,
        value: ethers.utils.parseEther(value),
    }
    return wallet.sign(transaction);
}
let signedTransaction = signTransaction(deriveFiveWalletsFromHDNode(mnemonic, "m/44'/60'/0'/0")[1], "0x933b946c4fec43372c5580096408d25b3c7936c5", "1.0")

console.log('Signed transaction: \n' + signedTransaction)

