import React, {Component} from 'react';
import Wallet from "../../../Crypto/Wallet";
import WalletClient from "../../../API/WalletClient";
import CoinFormat from "../../../CoinFormat";
import {BootstrapTable, TableHeaderColumn} from 'react-bootstrap-table';


export default class WalletView extends Component {
    constructor(props){
        super(props)
        this.state = {
            node: props.peers[Object.keys(props.peers)[0]],
            balance: 'Loading ...',
            balancePending: '-',
            receiveAddress: "Loading ...",
            send:{
                fee: 10,
                value: 1000000,
                to: '',
                data: '',
                error: ''
            },
            transactions: 'Loading ...'
        };
        this.lock = this.lock.bind(this);
        this.changeNode = this.changeNode.bind(this);
        this.reload = this.reload.bind(this);
        this.sendFunds = this.sendFunds.bind(this);
        this.handleInputChange = this.handleInputChange.bind(this);
        this.sentOrReceived = this.sentOrReceived.bind(this);

        this.wallet = Wallet.getInstatance();
        this.client = new WalletClient(this.state.node, this.wallet);
        this.addresses = Array.from({length: 10}, (x, i) => i).map((el) => {
            return this.wallet.account(el).getAddress()
        });
        this.loadBalance()
        this.loadTransactions()
    }
    lock(){
        Wallet.lock()
        this.props.coordinator.welcome()
    }

    changeNode(event){
        const selected = event.target.value
        this.setState({
            node: selected
        })
        this.client = new WalletClient(selected, this.wallet);
        this.loadBalance()
    }
    reload(event){
        event.preventDefault()
        this.setState({
            balance: 'Loading ...',
            balancePending: '-',
            receiveAddress: "Loading ...",
            transactions: "Loading ..."
        });
        this.loadBalance()
        this.loadTransactions()
    }
    async loadBalance(){
        const balance = await this.client.balance();
        this.setState({
            balance: new CoinFormat(balance.getTotalConfirmed()).toString(),
            balancePending: new CoinFormat(balance.getTotalUnconfirmed() - balance.getTotalConfirmed()).toString(),
            receiveAddress: Wallet.getInstatance().account(balance.unspentAccountNumber()).getAddress(),
        });
    }

    async loadTransactions() {
        const transactions = await this.client.transactions();
        this.setState({
            transactions: transactions
        });
        console.log(transactions);
    }

    handleInputChange(event){
        const target = event.target;
        let newSend = this.state.send;
        newSend[target.id] = target.value
        if (target.id === 'to'){
            if (!(/^[0-9a-f]{40}$/i.test(target.value))){
                newSend.error = "Invalid recepient address";
            } else {
                newSend.error = ""
            }
        }
        this.setState({
            send: newSend
        })
    }
    async sendFunds(event){
        var newSend = this.state.send;
        newSend.error = '';
        this.setState({
            send: newSend
        })
        event.preventDefault()
        try{
            const result = await this.client.send(this.state.send.to, this.state.send.value, this.state.send.fee, this.state.send.data)
            for (var res of result){
                if (res.success === false){
                    var newSend = this.state.send;
                    newSend.error = res.message;
                    this.setState({
                        send: newSend
                    })
                }
            }
            this.loadBalance()
        } catch (err){
            console.log(err)
            var newSend = this.state.send;
            newSend.error = err.toString();
            this.setState({
                send: newSend
            })
        }

    }
    priceFormat(value){
        return new CoinFormat(value).toString()
    }

    sentOrReceived(el, row){
        if (this.addresses.includes(row.from) && this.addresses.includes(row.to)){
            return "Self";
        }
        return this.addresses.includes(row.from) ? "Sent" : (this.addresses.includes(row.to)? "Received": "");
    }
    render() {
        const options = {
            sizePerPage: 5
        };
        const peers = this.props.peers;
        const linkTo = (link, dataElement) => {
            return (data, row) => {
                let linkData = null;
                if (typeof dataElement !== 'undefined'){
                    linkData = row[dataElement];
                } else {
                    linkData = data
                }
                return <a href={this.props.explorer + link + linkData}>{data}</a>
            }
        }
        return (
            <div className="container">
                <div className="row">
                    <div className="col-md-12">
                        <select value={this.state.node} onChange={this.changeNode}>{
                            Object.keys(peers).map(function (key){
                                return <option key={key} value={peers[key]}>{key}</option>
                            })
                        }</select>
                        <br/>
                        <br/>
                        <br/>
                        <ul className="nav nav-tabs nav-fill" id="myTab" role="tablist">
                            <li className="nav-item">
                                <a className="nav-link active" id="transactions-tab" data-toggle="tab" href="#transactions">Transactions</a>
                            </li>
                            <li className="nav-item">
                                <a className="nav-link" id="send-tab" data-toggle="tab" href="#send" >Send</a>
                            </li>
                            <li className="nav-item">
                                <a className="nav-link" id="receive-tab" data-toggle="tab" href="#receive">Receive</a>
                            </li>



                        </ul>
                        <div className="tab-content" id="myTabContent">
                            <div className="tab-pane fade show active" id="transactions" role="tabpanel"
                                 aria-labelledby="transactions-tab">

                                {typeof this.state.transactions === 'string' && this.state.transactions}
                                {typeof this.state.transactions === 'object' && <div>
                                    <BootstrapTable data={this.state.transactions} pagination={true} hover={true}
                                                    version='4' options={options}>
                                        <TableHeaderColumn dataField="hash" isKey dataSort={true} dataFormat={linkTo("/transaction/")}>Hash</TableHeaderColumn>
                                        <TableHeaderColumn dataField="from" dataFormat={linkTo("/address/")} dataSort={true}>From</TableHeaderColumn>
                                        <TableHeaderColumn dataField="to" dataSort={true}
                                                           dataFormat={linkTo("/address/")}>To</TableHeaderColumn>
                                        <TableHeaderColumn dataField="type" dataFormat={this.sentOrReceived}>Type</TableHeaderColumn>
                                        <TableHeaderColumn dataField="value" dataSort={true} dataFormat={this.priceFormat}>Value</TableHeaderColumn>
                                        <TableHeaderColumn dataField="fee" dataSort={true} dataFormat={this.priceFormat}>Fee</TableHeaderColumn>
                                        <TableHeaderColumn dataField="data">Data</TableHeaderColumn>
                                        <TableHeaderColumn dataField="mined_in_block_index"
                                                           dataFormat={linkTo("/block/", "mined_in_block_hash")} dataSort={true}>Block</TableHeaderColumn>
                                    </BootstrapTable>
                                </div>}

                            </div><div className="tab-pane fade" id="send" role="tabpanel"
                                 aria-labelledby="home-tab">

                            <form onSubmit={this.sendFunds}>
                                <div className={"form-group"}>
                                    <label htmlFor="exampleInputPassword1">Recipient Address</label>
                                    <input required="required" value={this.state.send.to} onChange={this.handleInputChange} type="integer"
                                           className={"form-control" + (this.state.send.error?' is-invalid':'')}
                                           id="to"
                                           placeholder="Recipient Address"/>
                                    {this.state.send.error &&
                                    <div className="invalid-feedback">{this.state.send.error}
                                    </div>
                                    }
                                </div>
                                <div className="form-group">
                                    <label htmlFor="value">Value</label>
                                    <div className="input-group">
                                    <input required="required" value={this.state.send.value} onChange={this.handleInputChange} type="number"
                                           className="form-control" id="value"
                                           placeholder="Value"/>
                                        <div className="input-group-append">
                                            <div className="input-group-text">nFs</div>
                                        </div>
                                    </div>

                                </div>
                                <div className={"form-group"}>
                                    <label htmlFor="exampleInputPassword1">Fee</label>
                                    <div className="input-group">
                                    <input required="required" value={this.state.send.fee} onChange={this.handleInputChange} type="number"
                                           className={"form-control"}
                                           id="fee"
                                           placeholder="Fee"/>
                                        <div className="input-group-append">
                                            <div className="input-group-text">nFs</div>
                                        </div>
                                    </div>

                                </div>
                                <div className={"form-group"}>
                                    <label htmlFor="exampleInputPassword1">Data</label>
                                    <textarea value={this.state.send.data} onChange={this.handleInputChange}
                                           className={"form-control"}
                                           id="data"
                                           placeholder="Data"/>

                                </div>

                                <button type="submit"
                                        className="btn btn-primary">Send
                                </button>

                            </form>


                            </div>
                            <div className="tab-pane fade" id="receive" role="tabpanel"
                                 aria-labelledby="profile-tab">
                                <br/>
                                <br/>
                                Send me money at:
                                <div className="alert alert-primary" role="alert">
                                {this.state.receiveAddress}
                                </div>
                            </div>

                        </div>
                        <br/>
                        <br/>
                        <br/>
                        <p>Balance</p>
                        <div className="alert alert-success" role="alert">
                            {this.state.balance} [{this.state.balancePending}] <a href="#" onClick={this.reload}>reload</a>
                        </div>
                        <a className="btn btn-warning" onClick={this.lock} href="#"
                           aria-label="Close">
                            Lock
                        </a>

                    </div>
                </div>
            </div>
        );
    }
}
