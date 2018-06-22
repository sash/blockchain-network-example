import React, { Component } from 'react';
import { BrowserRouter as Router , Route, Link } from 'react-router-dom'
import moment from 'moment';
import ExplorerClient from "../../API/ExplorerClient";

class Transaction extends Component {
    constructor(props){
        super(props)
        this.state = {
            tx: props.match.params.transactionHash
        };
        console.log('in the transactionnnn')
        this.client = new ExplorerClient(props.peers[props.match.params.node]);
        this.loadTransaction(props.match.params.transactionHash)
    }

    async loadTransaction(transactionHash){
        const transaction = await this.client.getTransaction(transactionHash)
        console.log(transaction)
        this.setState({
            tx: transaction
        })

    }

    render() {
        return (
            <div className="Transaction">
                <div class="row">
                    <div class="col-md-6 col-sm-6">
                        <table class="table table-striped">
                            <tbody>
                            <tr>
                                <th colspan="2">Summary</th>
                            </tr>
                            <tr>
                                <td>From</td>
                                <td><Link to={`/${this.props.match.params.node}/address/${this.state.tx.from}`}>{this.state.tx.from}</Link></td>
                            </tr>
                            <tr>
                                <td>To</td>
                                <td><Link to={`/${this.props.match.params.node}/address/${this.state.tx.to}`}>{this.state.tx.to}</Link></td>
                            </tr>
                            <tr>
                                <td>Value</td>
                                <td>{this.state.tx.value}</td>
                            </tr>
                            <tr>
                                <td>Fee</td>
                                <td>{this.state.tx.fee}</td>
                            </tr>
                            <tr>
                                <td>Mined in block</td>
                                <td>{this.state.tx.mined_in_block_index}</td>
                            </tr>
                            <tr>
                                <td>Timestamp</td>
                                <td>{this.state.tx.timestamp}</td>
                            </tr>
                            <tr>
                                <td>Timestamp for humans</td>
                                <td>{moment.unix(this.state.tx.timestamp).format("MMMM Do YYYY, h:mm:ss a")}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <table class="table table-striped">
                            <tbody>
                            <tr>
                                <th colspan="2">Hashes</th>
                            </tr>
                            <tr>
                                <td>Hash</td>
                                <td>{this.state.tx.hash}</td>
                            </tr>
                            <tr>
                                <td>Signature</td>
                                <td>{this.state.tx.signature}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        );
    }
}
export default Transaction;
