import React, { Component } from 'react';
//import logo from './logo.svg';
//import './style.css';
import ReactDOM from 'react-dom';
import Block from './Block';
import Home from './Home';
import { BrowserRouter as Router , Route, Link } from 'react-router-dom'


class App extends Component {
    constructor(props){
        super(props);
        console.log('in the app comp constructor');
        console.log(props)
    }

    render() {
        let {something} = this.props
        return (
            <div className="App">
                    <div className="App-header">
                    {/*<img src={logo} className="App-logo" alt="logo" />*/}
                    <h2>Block Explorer</h2>
                </div>
                <div className="App-nav">
                    <Router>
                        <div>
                            <Link to="/">Home</Link>
                            <Link to="/block">Block</Link>
                            <Route exact path="/" render={(props) => ( <Home peers={this.props.peers}/> )}/>
                            <Route exact path="/block" render={() => (
                                <h3>Please select a blockHash.</h3>
                            )}/>
                            <Route path="/block/:blockHash" component={Block}/>
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
