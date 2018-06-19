import React, {Component} from 'react';
import Wallet from "../../../Crypto/Wallet";
import WalletClient from "../../../API/WalletClient";

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
            }
        }
        this.lock = this.lock.bind(this)
        this.changeNode = this.changeNode.bind(this)
        this.reload = this.reload.bind(this)
        this.sendFunds = this.sendFunds.bind(this)
        this.handleInputChange = this.handleInputChange.bind(this)

        this.wallet = Wallet.getInstatance();
        this.client = new WalletClient(this.state.node, this.wallet);

        this.loadBalance()
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
        this.loadBalance()
    }
    async loadBalance(){
        const balance = await this.client.balance();
        this.setState({
            balance: balance.getTotalConfirmed(),
            balancePending: balance.getTotalUnconfirmed() - balance.getTotalConfirmed(),
            receiveAddress: Wallet.getInstatance().account(balance.unspentAccountNumber()).getAddress(),
        });
    }

    handleInputChange(event){
        const target = event.target;
        var newSend = this.state.send;
        newSend[target.id] = target.value
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
        } catch (e){
            var newSend = this.state.send;
            newSend.error = е.toString();
            this.setState({
                send: newSend
            })
        }

    }
    render() {
        const peers = this.props.peers;
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
                            <li className="nav-item">
                                <a className="nav-link btn"onClick={this.lock} href="#"
                                        aria-label="Close">
                                    Lock
                                </a>
                            </li>

                        </ul>
                        <div className="tab-content" id="myTabContent">
                            <div className="tab-pane fade show active" id="transactions" role="tabpanel"
                                 aria-labelledby="transactions-tab">Transactions
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
                                    <input required="required" value={this.state.send.value} onChange={this.handleInputChange} type="number"
                                           className="form-control" id="value"
                                           placeholder="Value"/>

                                </div>
                                <div className={"form-group"}>
                                    <label htmlFor="exampleInputPassword1">Fee</label>
                                    <input required="required" value={this.state.send.fee} onChange={this.handleInputChange} type="number"
                                           className={"form-control"}
                                           id="fee"
                                           placeholder="Fee"/>

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
                                 aria-labelledby="profile-tab">Send me money at:
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

                    </div>
                </div>
            </div>
        );
    }
}
