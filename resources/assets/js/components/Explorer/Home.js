import React, { Component } from 'react';
import _ from 'lodash';
import { Link } from 'react-router-dom';
import axios from 'axios';
import ExplorerClient from "../../API/ExplorerClient";

//import './style.css';

class Home extends Component {
    constructor(props) {
        super(props);
        console.log('in the home comp constructor');
        console.log(props);
        this.state = {
            node: props.peers[Object.keys(props.peers)[0]],
            blocks: ['1', '2','3'],
        };

        console.log(this.state);
        this.client = new ExplorerClient(this.state.node);
        this.loadBlocks()
    }

    componentWillMount() {
        console.log('will mounth Home comp');
    }

    async loadBlocks() {
        console.log('load blocks in home comp')
        const lastBlocks = await this.client.lastBlocks();


        this.setState({
            blocks: lastBlocks,
        });
        console.log('blocks are')
        console.log(this.state.blocks)
    }

    render() {
        console.log('render function');
        var tableRows = [];
        _.each(this.state.blocks, (value, index) => {
            tableRows.push(
                <tr key={this.state.blocks[index].block_hash}>
                    <td className="tdCenter">{this.state.blocks[index].id}</td>
                    <td><Link to={`/block/${this.state.blocks[index].block_hash}`}>{this.state.blocks[index].block_hash}</Link></td>
                    <td className="tdCenter">{this.state.blocks[index].txs}</td>
                </tr>
            )
        });

        return (
            <div className="Home">
                <table>
                    <thead><tr>
                        <th>Block No</th>
                        <th>Hash</th>
                        <th>TXs</th>
                    </tr></thead>
                    <tbody>
                    {tableRows}
                    </tbody>
                </table>
            </div>
        );
    }
}

export default Home;
