import Balance from "./Balance";
import CryptoJS from 'crypto-js';

export default class WalletClient{
    constructor(node_host, wallet){
        this.node_host = node_host;
        this.axios = window.axios;
        this.wallet = wallet;
        this.balances;
    }

    async balance(){
        // This is a cool way to get all balances using one request peer address, but the issue is that the server is bombarded with requests :)
        // var balances = [];
        // for(var i=0; i<10; i++){
        //     balances.push(this.balanceClient(i))
        // }
        // let listOfBalances = await Promise.all(balances)

        let listOfBalances = await this.balanceForAll()

        this.balances = new Balance(listOfBalances);
        return this.balances
    }

    async balanceForAll(){
        const addresses = Array.from({length: 10}, (x, i) => i).reduce((soFar, el) => {return soFar+this.wallet.account(el).getAddress()}, "");
        console.log(addresses);
        const res = await this.axios.get('http://' + this.node_host + '/api/balance/' + addresses)
        var response = res.data;
        response = response.map((el, index) => {
            el.accountNumber = index;
            return el;
        });
        console.log(response);
        // response.accountNumber = number
        return response
    }

    async balanceClient(number){
        const res = await this.axios.get('http://'+this.node_host+'/api/balance/'+this.wallet.account(number).getAddress())
        var response = res.data;
        response.accountNumber = number
        return response
    }

    async send(to, value, fee, notes){
        // Send from the oldest accounts untill the needed value is reached
        var transactions = []

        var remainingValue = value;

        var wallet = this.wallet;

        this.balances.accountNumbersWithFunds(function(accountNumber, numberOfSpends, funds){
            if(remainingValue > 0){
                const account = wallet.account(accountNumber);
                const value = Math.min(remainingValue, funds);
                const transaction = {
                    'from': account.getAddress(),
                    'from_id': numberOfSpends,
                    'to': to,
                    'value': value,
                    'fee': fee,
                    'data': notes,
                    'timestamp': Math.floor(Date.now() / 1000),
                };

                const toSign = JSON.stringify(transaction);
                const toSignHash = CryptoJS.SHA256(toSign).toString();
                transaction.hash = toSignHash;
                transaction.signature = account.sign(transaction.hash);


                console.log(transaction.signature);
                transactions.push(
                    transaction
                )
                remainingValue -= value;
            }
        });

        if (remainingValue > 0){
            throw new Error('Not enough funds to carry out the transaction');
        }
        const request = this.axios
        const host = this.node_host
        const responses = await Promise.all(

            transactions.map(async function (transaction){
                try{
                    return (await request.post('http://' + host + '/api/transaction', {transaction: transaction})).data
                }catch (e){
                    return e.response.data
                }
            })
        )
        return responses;
    }
}