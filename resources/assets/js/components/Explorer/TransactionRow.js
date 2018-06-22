import React, { Component } from 'react';
import moment from 'moment';
import { BrowserRouter as Router , Route, Link } from 'react-router-dom'

class TransactionRow extends Component {
    constructor(props){
        super(props)
        console.log('tx is here')
        console.log(props)
    }
    render() {
        return (
            <div>
                <table class="table table-striped">
                    <tbody>
                    <tr>
                        <th colspan="3" align="left">
                            <Link to={{pathname: `/transaction/${this.props.tx.hash}`, state: {tx:this.props.tx}}}>{this.props.tx.hash}</Link>
                            <span class="pull-right">{moment.unix(this.props.tx.timestamp).format("MMMM Do YYYY, h:mm:ss a")}</span>
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <Link to={`/address/${this.props.tx.from}`}>{this.props.tx.from}</Link>
                            {/*<a href="#">{this.props.tx.from}</a>*/}
                        </td>
                        <td>
                            <span>sends</span>
                        </td>
                        <td>
                            <Link to={`/address/${this.props.tx.to}`}>{this.props.tx.to}</Link>

                            {/*<a href="#">{this.props.tx.to}</a>*/}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        );
    }
}
export default TransactionRow;
