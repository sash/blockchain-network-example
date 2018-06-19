import React, {Component} from 'react';
import Wallet from "../../../Crypto/Wallet";

export default class NewWallet extends Component {
    constructor(props){
        super(props)
        this.handleInputChange = this.handleInputChange.bind(this);
        this.createWallet = this.createWallet.bind(this);
        this.isValid = this.isValid.bind(this);
        this.state = {
            password1: '',
            password2: '',
            error: '',
            mnemonic: ''
        };
    }
    createWallet(event){
        event.preventDefault();
        if (this.isValid()){
            try{
                Wallet.new(this.state.password1,this.state.password2);
                this.setState({mnemonic: Wallet.getInstatance().getMnemonic()})
            } catch (e) {
                this.setState({error: e.toString()})
            }
        }
    }
    handleInputChange(event){
        const target = event.target;
        if (target.id == "exampleInputPassword1"){
            this.setState({
                password1: target.value
            })
        } else {
            this.setState({
                password2: target.value
            })
        }
    }
    isValid(){
        return this.state.password1.length >= 6;
    }
    render() {
        return (
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-md-8">
                        <div className="card">
                            <div className="card-header ">Your new wallet
                                <button onClick={this.props.coordinator.welcome} type="button" className="close" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div className="card-body justify-content-left" style={{padding: 3+'em'}}>
                                {this.state.mnemonic ?
                                    <div>
                                        <div className="alert alert-primary" role="alert">
                                            {this.state.mnemonic}
                                        </div>
                                        <p>
                                        Write down your passphrase. You will not be able to recover your wallet without it!
                                        </p>
                                        <button className="btn" onClick={this.props.coordinator.wallet}>Continue to your wallet!</button>
                                     </div>

                                :
                                    <form onSubmit={this.createWallet}>
                                        <label htmlFor="exampleInputPassword1">Password for unlocking your new wallet</label>
                                        <div className="form-group">

                                            <input onChange={this.handleInputChange} type="password"
                                                   className="form-control" id="exampleInputPassword1"
                                                   aria-describedby="emailHelp" placeholder="Password"/>

                                        </div>
                                        <div className={"form-group" }>
                                            <input onChange={this.handleInputChange} type="password"
                                                   className={"form-control" + (this.state.error ? ' is-invalid' : '')} id="exampleInputPassword2"
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
                                }


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}
