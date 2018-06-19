import React, {Component} from 'react';
import Wallet from "../../../Crypto/Wallet";

export default class Restore extends Component {
    constructor(props) {
        super(props)
        this.handleInputChange = this.handleInputChange.bind(this);
        this.restoreWallet = this.restoreWallet.bind(this);
        this.isValid = this.isValid.bind(this);
        this.state = {
            password1: '',
            password2: '',
            error: '',
            mnemonic: ''
        };
    }

    restoreWallet(event) {
        event.preventDefault();
        if (this.isValid()) {
            try {
                Wallet.restore(this.state.mnemonic, this.state.password1, this.state.password2);
                this.props.coordinator.wallet()
            } catch (e) {
                this.setState({error: e.toString()})
            }
        }
    }

    handleInputChange(event) {
        const target = event.target;
        if (target.id == "exampleInputPassword1") {
            this.setState({
                password1: target.value
            })
        } else if (target.id == "exampleInputMnemonic") {
            this.setState({
                mnemonic: target.value
            })
        } else {
            this.setState({
                password2: target.value
            })
        }
    }

    isValid() {
        return this.state.password1.length >= 6 && this.state.mnemonic.length > 0;
    }
    render() {
        return (
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-md-8">
                        <div className="card">
                            <div className="card-header">Restore from passphrase
                                <button onClick={this.props.coordinator.welcome} type="button" className="close"
                                        aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div className="card-body" style={{padding: 3+'em'}}>
                                <form onSubmit={this.restoreWallet}>
                                    <label htmlFor="exampleInputPassword1">Your passphrase</label>
                                    <div className="form-group">

                                        <input onChange={this.handleInputChange} type="text"
                                               className="form-control" id="exampleInputMnemonic"
                                                placeholder="Passphrase"/>

                                    </div><label htmlFor="exampleInputPassword1">Password for unlocking your new
                                        wallet</label>
                                    <div className="form-group">

                                        <input onChange={this.handleInputChange} type="password"
                                               className="form-control" id="exampleInputPassword1"
                                                placeholder="Password"/>

                                    </div>
                                    <div className={"form-group"}>
                                        <input onChange={this.handleInputChange} type="password"
                                               className={"form-control" + (this.state.error ? ' is-invalid' : '')}
                                               id="exampleInputPassword2"
                                               placeholder="Confirm Password"/>
                                        {this.state.error &&
                                        <div className="invalid-feedback">{this.state.error}
                                        </div>
                                        }
                                    </div>

                                    <button disabled={!this.isValid()} type="submit"
                                            className="btn btn-primary">Create Wallet
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
