import React, {Component} from 'react';
import Wallet from "../../../Crypto/Wallet";

export default class Unlock extends Component {
    constructor(props) {
        super(props)
        this.handleInputChange = this.handleInputChange.bind(this);
        this.unlockWallet = this.unlockWallet.bind(this);
        this.isValid = this.isValid.bind(this);
        this.state = {
            password: '',
            error: ''
        };
    }

    unlockWallet(event) {
        event.preventDefault();
        if (this.isValid()) {
            try {
                Wallet.unlock(this.state.password);
                this.props.coordinator.wallet()
            } catch (e) {
                this.setState({error: e.toString()})
            }
        }
    }

    handleInputChange(event) {
        const target = event.target;

        this.setState({
            password: target.value
        })

    }

    isValid() {
        return this.state.password.length >= 6;
    }
    render() {
        return (
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-md-8">
                        <div className="card">
                            <div className="card-header">Unlock the wallet with your password
                                <button onClick={this.props.coordinator.welcome} type="button" className="close"
                                        aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div className="card-body" style={{padding: 3+'em'}}>
                                <form onSubmit={this.unlockWallet}>

                                    <div className={"form-group"}>
                                        <input onChange={this.handleInputChange} type="password"
                                               className={"form-control" + (this.state.error ? ' is-invalid' : '')} id="exampleInputPassword1"
                                               placeholder="Password"/>
                                        {this.state.error &&
                                        <div className="invalid-feedback">{this.state.error}
                                        </div>
                                        }
                                    </div>

                                    <button disabled={!this.isValid()} type="submit"
                                            className="btn btn-primary">Unlock
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}
