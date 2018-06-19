var CryptoJS = require("crypto-js");

process.on('message', async (message) => {
    let {nonce, timestamp, blockHash} = mineBlock(message.data_hash, message.difficulty, message.startFrom, message.increment);

    process.send({nonce:nonce, timestamp:timestamp, blockHash:blockHash, startFrom: message.startFrom});
});

var mineBlock = function (data_hash, difficulty, startFrom, increment) {
    nonce = startFrom;
    var blockHash = '';

    while(true){
        var timestamp = new Date().getTime() / 1000;
        blockHash = calculateHash(data_hash, nonce, timestamp);

        if(blockHash.substr(0, difficulty) == Array(difficulty+1).join("0")){
            // we have successfully mined a block
            break;
        }

        //trying again
        nonce += increment;
    }

    return {nonce, timestamp, blockHash};
};

var calculateHash = function (data_hash, nonce, timestamp){
    return CryptoJS.SHA256(data_hash+timestamp+nonce).toString();
};
