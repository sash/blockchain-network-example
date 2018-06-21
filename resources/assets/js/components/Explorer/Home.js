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
            curr_block: null
        };

        console.log(this.state);
        this.client = new ExplorerClient(this.state.node);
        this.loadBlocks(10)
    }

    componentWillMount() {
        console.log('will mounth Home comp');
        this.setState({
            curr_block: curr_block_no
        });
    }

    async loadBlocks(curr_block_no) {
        console.log('load blocks in home comp')
        const lastBlocks = await this.client.lastBlocks();

        this.setState({
            blocks: lastBlocks,
        });
    }

    render() {
        console.log('render function');
        var tableRows = [];
        _.each(this.state.blocks, (value, index) => {
            tableRows.push(
                <tr key={this.state.blocks[index].block_hash}>
                    <td className="tdCenter">{this.state.blocks[index].id}</td>
                    <td><Link to={`/block/${this.state.blocks[index].block_hash}`}>{this.state.blocks[index].block_hash}</Link></td>
                </tr>
            )
        });

        return (
            <div className="Home">
                <h2>Home page</h2>
                Current Block: {this.state.curr_block}
                <table>
                    <thead><tr>
                        <th>Block No</th>
                        <th>Hash</th>
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
