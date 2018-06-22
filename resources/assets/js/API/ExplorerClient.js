import CryptoJS from 'crypto-js';
import Balance from "./Balance";

export default class ExplorerClient{
    constructor(node_host) {
        this.node_host = node_host;
        this.axios = window.axios;
    }

    async lastBlocks(){
        const res = await this.axios.get('http://'+this.node_host+'/api/blocks/last/10')
        var response = res.data;
        console.log(response);
        return response
    }

    async getBlock(blockHash){
        const res = await this.axios.get('http://'+this.node_host+'/api/blocks/'+blockHash)
        var response = res.data;
        console.log(response);
        return response
    }

    async balanceForAddress(address){

        const res = await this.axios.get('http://' + this.node_host + '/api/balance/' + address)
        let response = res.data;

        console.log('balance fetched')
        console.log(response)

        return response
        //return new Balance(response)
    }

    async transactionsFor(address){
        const res = await this.axios.get('http://' + this.node_host + '/api/transactions/address/' + address);

        return res.data;
    }
}
