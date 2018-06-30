pragma solidity ^0.4.22;

contract Storage {
    uint private localStorage;
    function set(uint value) public {
        localStorage = value;
    }
    function get() view public returns (uint){
        return localStorage;
    }
}
