pragma solidity ^0.4.22;

contract incrementer {
    uint private valueStorage;
    constructor() public {
        valueStorage = 0;
    }
    function value() view public returns (uint) {
        return valueStorage;
    }

    function increment(uint amount) public {
        valueStorage += amount;
    }
}
