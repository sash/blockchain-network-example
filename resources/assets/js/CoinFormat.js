export default class CoinFormat{
    constructor(value){
        this.value = value
    }
    toString(){
        if (this.value > 1000000) {
            return this.frauds();
        }
        else if(this.value > 1000)
        {
            return this.microFrauds();
        }
        else
        {
            return this.nanoFrauds();
        }
    }


    frauds() {
        return (this.value / 1000000)+
        'Fs';
    }


    microFrauds() {
        return (this.value / 1000)+
        'mFs';
    }


    nanoFrauds() {
        return this.value+
        'nFs';
    }
}