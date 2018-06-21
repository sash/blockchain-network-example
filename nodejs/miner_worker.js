var CryptoJS = require("crypto-js");

process.on('message', async (message) => {
    let {nonce, timestamp, blockHash} = await mineBlock(message.data_hash, message.difficulty, message.startFrom, message.increment);

    process.send({nonce:nonce, timestamp:timestamp, blockHash:blockHash, startFrom: message.startFrom});
});

function sleep(millis) {
    return new Promise(resolve => setTimeout(resolve, millis));
}

async function mineBlock(data_hash, difficulty, startFrom, increment) {
    nonce = startFrom;
    var blockHash = '';

    while(true){
        var timestamp = Math.floor(new Date().getTime() / 1000);
        blockHash = await calculateHash(data_hash, nonce, timestamp);

        if(blockHash.substr(0, difficulty) == Array(difficulty+1).join("0")){
            // we have successfully mined a block
            break;
        }

        //trying again
        nonce += increment;
    }

    return {nonce, timestamp, blockHash};
}

async function calculateHash(data_hash, nonce, timestamp) {
    await sleep(1000);
    return CryptoJS.SHA256(data_hash+timestamp+nonce).toString();
}
