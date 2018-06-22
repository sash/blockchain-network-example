import React, { Component } from 'react';
import CoinFormat from "../../CoinFormat";
import moment from 'moment';
import { BrowserRouter as Router , Route, Link } from 'react-router-dom'

class TransactionRow extends Component {
    constructor(props){
        super(props)
    }
    render() {
        return (
            <div>
                <table className="table table-striped">
                    <tbody>
                    <tr >
                        <th colSpan="4" align="left">
                            <Link to={{pathname: `/${this.props.node}/transaction/${this.props.tx.hash}`, state: {tx:this.props.tx}}}>{this.props.tx.hash}</Link>
                            <span style={{float:"right"}} className="pull-right">{moment.unix(this.props.tx.timestamp).format("MMMM Do YYYY, h:mm:ss a")}</span>
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <Link to={`/${this.props.node}/address/${this.props.tx.from}`}>{this.props.tx.from}</Link>
                        </td>
                        <td>
                            <span>sends</span>
                        </td>
                        <td>
                            <Link to={`/${this.props.node}/address/${this.props.tx.to}`}>{this.props.tx.to}</Link>
                        </td>
                        <td>
                            <span>{new CoinFormat(this.props.tx.value).toString()}</span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        );
    }
}
export default TransactionRow;
