pragma solidity ^0.4.0;

contract SimpleToken {
    mapping(address => uint) private balances;
    constructor(uint initialSupply) public{
        balances[msg.sender] = initialSupply;
    }
    function transfer(address to, uint value) public {
        require(value > 0);
        require(balances[msg.sender] >= value);
        require(balances[to] + value > balances[to]); // overflow protection
        balances[msg.sender] -= value;
        balances[to] += value;
    }
    function tokens(address addr) view public returns (uint){
        return balances[addr];
    }
    function myTokens() view public returns (uint){
        return tokens(msg.sender);
    }
}
