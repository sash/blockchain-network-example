import React, { Component } from 'react';
import ExplorerClient from "../../API/ExplorerClient";
import CoinFormat from "../../CoinFormat";
import TransactionRow from "./TransactionRow"

//import './style.css';

class Address extends Component {
    constructor(props){
        super(props)
        this.state = {
            address: props.match.params.addressHash,
            node: props.peers[Object.keys(props.peers)[0]],
            balance: 'Loading ...',
            balancePending: '-',
            transactions: "Loading ...",
        };
        this.client = new ExplorerClient(this.state.node);
        this.loadBalanceFor(this.state.address)
        this.loadTransactionsFor(this.state.address)
    }

    componentWillReceiveProps(nextProps){
        if(nextProps.match.params.addressHash !== this.state.address){
            this.setState({address: nextProps.match.params.addressHash},function(){
                this.loadBalanceFor(this.state.address)
                this.loadTransactionsFor(this.state.address)
            }.bind(this));
        }
    }

    async loadBalanceFor(address){
        const balance = await this.client.balanceForAddress(address);
        this.setState({
            balance: new CoinFormat(balance.confirmed).toString(),
            balancePending: new CoinFormat(balance.unconfirmed - balance.confirmed).toString(),
        });
    }

    async loadTransactionsFor(address) {
        const transactions = await this.client.transactionsFor(address);
        this.setState({
            transactions: transactions
        });
    }

    render() {
        var tableRows = [];
        _.each(this.state.transactions, (value, index) => {
            tableRows.push(
                <TransactionRow key={index} tx={value} />
            )
        });

        return (
            <div className="Address">
                <div class="row">
                    <div class="col-md-6 col-sm-6">
                        <table class="table table-striped">
                            <tbody>
                            <tr>
                                <th colspan="2">Address {this.state.address}</th>
                            </tr>
                            <tr>
                                <td>Confirmed Balance:</td>
                                <td>{this.state.balance}</td>
                            </tr>
                            <tr>
                                <td>Pending Balance</td>
                                <td>{this.state.balancePending}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6 col-sm-6">

                    </div>
                </div>
                <div>
                    <h2>Transactions</h2>
                    {tableRows}
                </div>
            </div>
        );
    }
}
export default Address;
