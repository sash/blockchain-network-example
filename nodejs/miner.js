let request = require('request');

const { fork } = require('child_process');

let new_job_endpoint = 'http://'+process.env.NODE_HOST+"/api/miner/job/"+ process.env.ADDRESS;
let submit_job_endpoint = 'http://'+process.env.NODE_HOST+"/api/miner/job";
let get_latest_block_hash_endpoint = 'http://'+process.env.NODE_HOST+"/api/miner/last-block-hash";
let previous_block_hash='';
let miningProcesses = [];
let concurrentWorkers = 4;
//let candidateBlock = {};

setInterval(function(){
    request(get_latest_block_hash_endpoint, function (error, response, body) {
        let data = JSON.parse(response.body);

        if(previous_block_hash !== data['hash'] && previous_block_hash!==''){
            // console.log('current_previous_block_hash: '+previous_block_hash+', hash: '+data['hash']);
            console.log('Just found out new block to mine. Starting in a second...');
            miningProcesses.forEach(function (p) {
                p.kill('SIGKILL');
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
        let candidateBlock = data;

        console.log('New block to mine! Difficulty: '+difficulty+', data_hash: '+data_hash+', previous_block_hash: '+previous_block_hash);
        console.log(JSON.stringify(candidateBlock));

        for (let i = 0; i < concurrentWorkers; i++) {
            miningProcesses[i] = fork('nodejs/miner_worker.js');
            miningProcesses[i].send({data_hash, difficulty, startFrom: i, increment: concurrentWorkers});
        }

        miningProcesses.forEach(function (proc) {
            proc.on('message', (message) => {
                console.log('block is mined!');
                console.log('index: ' + candidateBlock.index);
                console.log('nonce: ' + message.nonce);
                console.log('timestamp ' + message.timestamp);
                console.log('blockHash ' + message.blockHash);
                console.log('from ' + message.startFrom);

                notifyNode(message, candidateBlock);

                miningProcesses.forEach(function (p) {
                    p.kill('SIGKILL');
                });


            })
        });
    });
};

let notifyNode = function (message, candidateBlock){


    //glue the additional data to the block in order to send to the node
    candidateBlock['nonce'] = message.nonce;
    candidateBlock['timestamp'] = message.timestamp;
    let options = {
        url: submit_job_endpoint,
        method: 'POST',
        headers: {'Content-Type': 'application/json','Accept': 'application/json'},
        body: JSON.stringify({
            'block': candidateBlock
        })
    };
    console.log('Notifying node for new block', JSON.stringify(candidateBlock));

    request(options, function (errors, response, body){
        if (!errors && (response.statusCode == 200 || response.statusCode == 201)) {
            console.log('Node response code: ', response.statusCode);
        } else {
            const bodyArray = JSON.parse(body)
            console.log('Error in block', JSON.stringify(candidateBlock), errors, response.statusCode, bodyArray.message);
        }
        requestNewJobAndStartMining()
    });
};

requestNewJobAndStartMining();
