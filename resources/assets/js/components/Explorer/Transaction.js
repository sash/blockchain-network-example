import React, { Component } from 'react';
import { BrowserRouter as Router , Route, Link } from 'react-router-dom'
import moment from 'moment';

//import './style.css';

class Transaction extends Component {
    constructor(props){
        super(props)
        console.log(props)
        this.state = {
            tx: props.location.state.tx
        };
    }

    render() {
        return (
            <div className="Block">
                <div class="row">
                    <div class="col-md-6 col-sm-6">
                        <table class="table table-striped">
                            <tbody>
                            <tr>
                                <th colspan="2">Summary</th>
                            </tr>
                            <tr>
                                <td>From</td>
                                <td><Link to={`/address/${this.state.tx.from}`}>{this.state.tx.from}</Link></td>
                            </tr>
                            <tr>
                                <td>To</td>
                                <td><Link to={`/address/${this.state.tx.to}`}>{this.state.tx.to}</Link></td>
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
