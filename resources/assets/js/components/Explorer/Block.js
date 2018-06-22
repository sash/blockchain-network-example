import React, { Component } from 'react';
import ExplorerClient from "../../API/ExplorerClient";
import TransactionRow from "./TransactionRow"
import { Link } from 'react-router-dom';

//import './style.css';

class Block extends Component {
    constructor(props){
        super(props)
        this.state = {
            node: props.peers[Object.keys(props.peers)[0]],
            block:{},
        };
        this.client = new ExplorerClient(this.state.node);
        this.loadBlock(props.match.params.blockHash)
    }

    async loadBlock(blockHash){
        const block = await this.client.getBlock(blockHash)
        this.setState({
            block: block
        })
        console.log(this.state.block)
    }

    render() {
        var tableRows = [];
        _.each(this.state.block.transactions, (value, index) => {
            tableRows.push(
                <TransactionRow key={index} tx={value} />
            )
        });

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
                                    <td>Number of transactions</td>
                                    <td>txs count</td>
                                </tr>
                                <tr>
                                    <td>Difficulty</td>
                                    <td>{this.state.block.difficulty}</td>
                                </tr>
                                <tr>
                                    <td>Cumulative difficulty</td>
                                    <td>{this.state.block.cumulativeDifficulty}</td>
                                </tr>
                                <tr>
                                    <td>Mined by Address</td>
                                    <td>{this.state.block.mined_by_address}</td>
                                </tr>
                                <tr>
                                    <td>Nonce</td>
                                    <td>{this.state.block.nonce}</td>
                                </tr>
                                <tr>
                                    <td>Timestamp</td>
                                    <td>{this.state.block.timestamp}</td>
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
                                    <td><Link to={`/block/${this.state.block.block_hash}`}>{this.state.block.block_hash}</Link></td>
                                    {/*<td>{this.state.block.block_hash}</td>*/}
                                </tr>
                                <tr>
                                    <td>Previous Block</td>
                                    <td>{this.state.block.previous_block_hash}</td>
                                </tr>
                            </tbody>
                        </table>
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
export default Block;
