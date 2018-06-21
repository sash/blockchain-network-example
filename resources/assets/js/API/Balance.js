export default class Balance{
    constructor(listOfBalances){
        this.listOfBalances = listOfBalances;
        console.log(listOfBalances)
    }
    getTotalConfirmed(){
        return this.listOfBalances.reduce(function(soFar, el){
            return soFar + el.confirmed;
        }, 0);
    }
    getTotalUnconfirmed(){
        return this.listOfBalances.reduce(function (soFar, el) {
            return soFar + el.unconfirmed;
        }, 0);
    }
    unspentAccountNumber(){
        for (var balance in this.listOfBalances){
            if (this.listOfBalances[balance].txs === 0){
                return this.listOfBalances[balance].accountNumber;
            }
        }
        throw new Error('No unspent addresses found')
    }

    accountNumbersWithFunds(callback){
        for (var balance in this.listOfBalances) {

            if (this.listOfBalances[balance].confirmed > 0) {
                callback(this.listOfBalances[balance].accountNumber, this.listOfBalances[balance].txs, this.listOfBalances[balance].confirmed)
            }
        }
    }
}