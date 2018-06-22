import CryptoJS from 'crypto-js';

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
}
