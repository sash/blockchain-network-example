import React from 'react';
import ReactDOM from "react-dom";
import Welcome from "./Scenes/Welcome";
import NewWallet from "./Scenes/NewWallet";
import WalletView from "./Scenes/WalletView";
import Unlock from "./Scenes/Unlock";
import Restore from "./Scenes/Restore";

export default class Coordinator{
    constructor(appElement, peers){
        this.create = this.create.bind(this)
        this.restore = this.restore.bind(this)
        this.wallet = this.wallet.bind(this)
        this.unlock = this.unlock.bind(this)
        this.welcome = this.welcome.bind(this)
        this.peers = peers
        this.appElement = appElement
        this.welcome();
    }

    welcome(){
        ReactDOM.render(<Welcome coordinator={this}/>, this.appElement);
    }
    create(){
        ReactDOM.render(<NewWallet coordinator={this}/>, this.appElement);
    }

    unlock() {
        ReactDOM.render(<Unlock coordinator={this}/>, this.appElement);
    }
    restore(){
        ReactDOM.render(<Restore coordinator={this}/>, this.appElement);
    }
    wallet(){
        ReactDOM.render(<WalletView peers={this.peers} coordinator={this}/>, this.appElement);
    }
}