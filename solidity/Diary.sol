pragma solidity ^0.4.0;

contract Diary {
    address private alice;
    string[] private entries;
    mapping(address => bool) private readers;

    event NewFact(uint index);

    constructor() public{
        alice = msg.sender;
        readers[0x14723A09ACff6D2A60DcdF7aA4AFf308FDDC160C] = true;
        readers[0xCA35b7d915458EF540aDe6068dFe2F44E8fa733c] = true;
    }
    modifier onlyAlice(){
        require(msg.sender == alice);
        _;
    }
    modifier onlyReaders(){
        require(readers[msg.sender] == true);
        _;
    }
    function addFact(string fact) public onlyAlice {
        entries.push(fact);
        emit NewFact(entries.length - 1);
    }

    function getFact(uint index) view public onlyReaders returns (string) {
        require(entries.length > index);
        return entries[index];
    }

    function countFacts() view public returns (uint){
        return entries.length;
    }
}
