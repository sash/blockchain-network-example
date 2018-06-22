import React, { Component } from 'react';
//import logo from './logo.svg';
//import './style.css';
import ReactDOM from 'react-dom';
import Block from './Block';
import Transaction from './Transaction';
import Home from './Home';
import { BrowserRouter as Router , Route, Link } from 'react-router-dom'


class App extends Component {
    constructor(props){
        super(props);
        console.log('in the app comp constructor');
        console.log(props)
    }

    render() {
        return (
            <div className="App">
                    <div className="App-header">
                    {/*<img src={logo} className="App-logo" alt="logo" />*/}
                </div>
                <div className="App-nav">
                    <Router>
                        <div>
                            <Link to="/">Last 10</Link>
                            <Route exact path="/" render={(props) => ( <Home peers={this.props.peers}/> )}/>
                            <Route path="/block/:blockHash" render={(props) => ( <Block {...props} peers={this.props.peers}/> )}/>
                            <Route path="/transaction/:transactionHash" render={(props) => ( <Transaction {...props} /> )}/>
                        </div>
                    </Router>
                </div>
            </div>
    );
    }
}



export default App;

// if (document.getElementById('app')) {
//     ReactDOM.render(<App />, document.getElementById('app'));
// }
