import React, { Component } from 'react';
import _ from 'lodash';
import { Link } from 'react-router-dom';
import axios from 'axios';
import ExplorerClient from "../../API/ExplorerClient";

//import './style.css';

class Home extends Component {
    constructor(props) {
        super(props);
        this.state = {
            blocks: [],
        };

        this.client = new ExplorerClient(props.peers[props.match.params.node]);
        this.loadBlocks()
    }

    componentDidUpdate(prevProps)
    {
        if(prevProps.match.params.node !== this.props.match.params.node){
        this.client = new ExplorerClient(this.props.peers[this.props.match.params.node]);
            this.loadBlocks()
        }
    }

    async loadBlocks() {
        const lastBlocks = await this.client.lastBlocks();

        this.setState({
            blocks: lastBlocks,
        });
    }

    render() {
        let tableRows = [];
        _.each(this.state.blocks, (value, index) => {
            tableRows.push(
                <tr key={this.state.blocks[index].block_hash}>
                    <td className="tdCenter">{this.state.blocks[index].index}</td>
                    <td><Link to={`/${this.props.match.params.node}/block/${this.state.blocks[index].block_hash}`}>{this.state.blocks[index].block_hash}</Link></td>
                    <td className="tdCenter">{this.state.blocks[index].txs}</td>
                </tr>
            )
        });

        return (
            <div className="Home">
                <table>
                    <thead><tr>
                        <th>Block Index</th>
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
