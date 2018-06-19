import React, {Component} from 'react';
import Wallet from "../../../Crypto/Wallet";

export default class Welcome extends Component {
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
                            <div className="card-header">Welcome</div>

                            <div className="card-body" style={{padding: 3+'em'}}>
                                {Wallet.hasStorage() && <div>
                                <h2>Unlock your wallet</h2>

                                <form onSubmit={this.unlockWallet}>
                                    <div className="form-row">
                                    <div className="col-md-10 mb-3">
                                        <input onChange={this.handleInputChange} type="password"
                                               className={"form-control" + (this.state.error ? ' is-invalid' : '')}
                                               id="exampleInputPassword1"
                                               placeholder="Password"/>
                                        {this.state.error &&
                                        <div className="invalid-feedback">{this.state.error}
                                        </div>
                                        }
                                    </div>
                                        <div className="col-md-2 mb-3">
                                    <button disabled={!this.isValid()} type="submit"
                                            className="btn btn-primary mb-2">Unlock
                                    </button>
                                        </div>
                                    </div>
                                </form>
                                <hr/>
                                <p>... or ...</p>
                                </div>}
                                <div className="btn-group">

                                    <button onClick={this.props.coordinator.create}
                                            className="btn btn-outline-info">Create new wallet
                                    </button>
                                    <button onClick={this.props.coordinator.restore}
                                            className="btn btn-outline-secondary">Restore
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}
