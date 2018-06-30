pragma solidity ^0.4.22;

contract LastInvoker {
    address private lastInvoker;
    constructor() public{
        lastInvoker = 0x0;
    }
    event LastInvokerChanged(address newInvoker);
    function getLastInvoker() public returns (bool, address)
    {
        address previousInvoker = lastInvoker;
        lastInvoker = msg.sender;
        emit LastInvokerChanged(lastInvoker);
        return (previousInvoker != 0x0, previousInvoker);
    }
}
