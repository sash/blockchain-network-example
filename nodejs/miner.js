var request = require('request');

const { fork } = require('child_process')

let get_new_job_endpoint = 'http://'+process.env.NODE_HOST+"/api/miner/job/miner-address"
let post_mined_block_endpoint = 'http://'+process.env.NODE_HOST+"/api/miner/job"

console.log(process.env.NODE_HOST);

// setInterval(function(){
//     console.log('im In the set interval')
// }, 100)

request(get_new_job_endpoint, function (error, response, body) {
    var data = JSON.parse(response.body);
    var difficulty = data['difficulty'];
    var data_hash = data['data_hash'];

    console.log('Going to mine with difficulty: '+difficulty);

    var miningProcess = [];
    miningProcess[0] = fork('nodejs/onlymines.js');
    miningProcess[1] = fork('nodejs/onlymines.js');
    miningProcess[2] = fork('nodejs/onlymines.js');
    miningProcess[3] = fork('nodejs/onlymines.js');
    miningProcess[4] = fork('nodejs/onlymines.js');
    miningProcess[5] = fork('nodejs/onlymines.js');
    miningProcess[6] = fork('nodejs/onlymines.js');
    miningProcess[7] = fork('nodejs/onlymines.js');
    miningProcess[0].send({data_hash, difficulty, startFrom: 0, increment: 8});
    miningProcess[1].send({data_hash, difficulty, startFrom: 1, increment: 8});
    miningProcess[2].send({data_hash, difficulty, startFrom: 2, increment: 8});
    miningProcess[3].send({data_hash, difficulty, startFrom: 3, increment: 8});
    miningProcess[4].send({data_hash, difficulty, startFrom: 4, increment: 8});
    miningProcess[5].send({data_hash, difficulty, startFrom: 5, increment: 8});
    miningProcess[6].send({data_hash, difficulty, startFrom: 6, increment: 8});
    miningProcess[7].send({data_hash, difficulty, startFrom: 7, increment: 8});

    miningProcess.forEach(function(proc) {
        proc.on('message', (message) => {
            console.log('nonce: ' + message.nonce);
            console.log('timestamp ' + message.timestamp)
            console.log('blockHash ' + message.blockHash)
            console.log('from ' + message.startFrom)
            console.log('block is mined!')
            miningProcess.forEach(function(p){
                p.kill();
            });
        })
    });
});

