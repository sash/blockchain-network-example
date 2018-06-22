import CryptoJS from 'crypto-js';
import Balance from "./Balance";


export default class ExplorerClient{
    constructor(node_host) {
        this.node_host = node_host
        this.axios = window.axios;
    }

    async lastBlocks(){
        console.log('fetching last 10 from '+this.node_host)
        const res = await this.axios.get('http://'+this.node_host+'/api/blocks/last/20')
        var response = res.data;
        return response
    }

    async getBlock(blockHash){
        console.log('fetching blocks from '+this.node_host)
        const res = await this.axios.get('http://'+this.node_host+'/api/blocks/'+blockHash)
        var response = res.data;
        return response
    }

    async getTransaction(transactionHash){
        console.log('fetching transaction from '+this.node_host)
        const res = await this.axios.get('http://'+this.node_host+'/api/transactions/'+transactionHash)
        var response = res.data;
        return response
    }

    async balanceForAddress(address){
        console.log('fetching balance for '+address+' from '+this.node_host)
        const res = await this.axios.get('http://' + this.node_host + '/api/balance/' + address)
        let response = res.data;

        return response
    }

    async transactionsFor(address){
        console.log('fetching txs for '+address+' from '+this.node_host)
        const res = await this.axios.get('http://' + this.node_host + '/api/transactions/address/' + address);

        return res.data;
    }
}
