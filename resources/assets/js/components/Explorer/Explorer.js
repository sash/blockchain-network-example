import React from 'react';
import ReactDOM from "react-dom";
import App from "./App";

export default class Explorer{
    constructor(appElement, peers){
        this.peers = peers
        this.appElement = appElement
        this.explorer();
    }

    explorer(){
        ReactDOM.render(<App coordinator={this} peers={this.peers}/>, this.appElement);
    }
}
