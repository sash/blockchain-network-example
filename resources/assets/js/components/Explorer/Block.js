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
        this.client = new ExplorerClient(props.peers[props.match.params.node]);
        this.loadBlock(props.match.params.blockHash)
    }

    componentDidUpdate(prevProps)
    {
        if(prevProps.match.params.node !== this.props.match.params.node){
            this.client = new ExplorerClient(this.props.peers[this.props.match.params.node]);
            this.loadBlock(this.props.match.params.blockHash)
        }

        if(prevProps.match.params.blockHash !== this.props.match.params.blockHash){
            this.loadBlock(this.props.match.params.blockHash)
        }
    }

    async loadBlock(blockHash){
        const block = await this.client.getBlock(blockHash)
        this.setState({
            block: block
        })
    }

    render() {
        var tableRows = [];
        _.each(this.state.block.transactions, (value, index) => {
            tableRows.push(
                <TransactionRow key={index} tx={value} node={this.props.match.params.node} />
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
                                    <td>{_.size(this.state.block.transactions)}</td>
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
                                    <td><Link to={`/${this.props.match.params.node}/address/${this.state.block.mined_by_address}`}>{this.state.block.mined_by_address}</Link></td>
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
                                    <td><Link to={`/${this.props.match.params.node}/block/${this.state.block.block_hash}`}>{this.state.block.block_hash}</Link></td>
                                </tr>
                                <tr>
                                    <td>Previous Block</td>
                                    <td><Link to={`/${this.props.match.params.node}/block/${this.state.block.previous_block_hash}`}>{this.state.block.previous_block_hash}</Link></td>
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
