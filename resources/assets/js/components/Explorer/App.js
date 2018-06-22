import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import Block from './Block';
import Transaction from './Transaction';
import Address from './Address';
import Home from './Home';
import { BrowserRouter as Router , Route, Link } from 'react-router-dom'
import ExplorerClient from "../../API/ExplorerClient";


class App extends Component {
    constructor(props){
        super(props);
        this.state = {
            node: props.peers[Object.keys(props.peers)[0]]
        };
        console.log(props.peers);
        this.changeNode = this.changeNode.bind(this);
    }

    changeNode(event){
        const selected = event.target.value
        console.log(event.nativeEvent.target.selectedIndex)

        let index = event.nativeEvent.target.selectedIndex;
        this.props.history.push("/"+event.nativeEvent.target[index].text)

        this.setState({node: selected }, function(){
            console.log(event)
        }.bind(this))
    }

    render() {
        const peers = this.props.peers;

    return (
        <Router>
            <div className="App">

                <div className="App-header">
                    {/*<select value={this.state.node} onChange={this.changeNode}>*/}
                    {
                        Object.keys(peers).map(function (key){
                            return <Link to={`/${key}`} key={key}>{key}</Link>
                        })
                    }
                    {/*</select>*/}
                </div>
                <div className="App-nav">

                    <div>
                        <Route exact path="/:node" render={(props) => ( <Home {...props} peers={this.props.peers} /> )}/>
                        <Route path="/:node/block/:blockHash" render={(props) => ( <Block {...props}  peers={this.props.peers} /> )}/>
                        <Route path="/:node/transaction/:transactionHash" render={(props) => ( <Transaction {...props} peers={this.props.peers} /> )}/>
                        <Route path="/:node/address/:addressHash" render={(props) => ( <Address {...props} peers={this.props.peers} /> )}/>
                    </div>

                </div>
            </div>
        </Router>
    )}
}



export default App;

// if (document.getElementById('app')) {
//     ReactDOM.render(<App />, document.getElementById('app'));
// }
