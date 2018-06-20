let request = require('request');

const { fork } = require('child_process');

let new_job_endpoint = 'http://'+process.env.NODE_HOST+"/api/miner/job/miner-address"
let submit_job_endpoint = 'http://'+process.env.NODE_HOST+"/api/miner/job"
let get_latest_block_hash = 'http://'+process.env.NODE_HOST+"/api/miner/last-block-hash";
let previous_block_hash='';
let miningProcesses = [];
let candidateBlock = {};

setInterval(function(){
    request(get_latest_block_hash, function (error, response, body) {
        let data = JSON.parse(response.body);

        if(previous_block_hash !== data['hash']){
            console.log('current_previous_block_hash: '+previous_block_hash+', hash: '+data['hash']);
            console.log('Just found out new block to mine. Starting in a second...');
            miningProcesses.forEach(function (p) {
                p.kill();
            });

            requestNewJobAndStartMining()
        }
    })
}, 5000);

let requestNewJobAndStartMining = function() {
    request(new_job_endpoint, function (error, response, body) {
        let data = JSON.parse(response.body);
        let difficulty = data['difficulty'];
        let data_hash = data['data_hash'];
        previous_block_hash = data['previous_block_hash'];
        candidateBlock = data;

        console.log('New block to mine! Difficulty: '+difficulty+', data_hash: '+data_hash+', previous_block_hash: '+previous_block_hash);

        for (let i = 0; i < 8; i++) {
            miningProcesses[i] = fork('nodejs/miner_worker.js');
            miningProcesses[i].send({data_hash, difficulty, startFrom: i, increment: 8});
        }

        miningProcesses.forEach(function (proc) {
            proc.on('message', (message) => {
                console.log('nonce: ' + message.nonce);
                console.log('timestamp ' + message.timestamp);
                console.log('blockHash ' + message.blockHash);
                console.log('from ' + message.startFrom);
                console.log('block is mined!');

                notifyNode(message);

                miningProcesses.forEach(function (p) {
                    p.kill();
                });

                requestNewJobAndStartMining()
            })
        });
    });
};

let notifyNode = function (message){
    console.log('Notifying node for new block');

    //glue the additional data to the block in order to send to the node
    candidateBlock['nonce'] = message.nonce;
    candidateBlock['timestamp'] = message.timestamp;

    let options = {
        url: submit_job_endpoint,
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            'block': candidateBlock
        })
    };

    request(options, function (errors, response, body){
        if (!errors && response.statusCode == 200) {
            console.log('Node response code: ', response.statusCode);
        } else {
            console.log('Error', errors, response.statusCode, body);
        }
    });
};

requestNewJobAndStartMining();
