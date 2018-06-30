pragma solidity ^0.4.0;

/**
 * I have expanded the example a bit in order to play around some more
 */
contract Certificates {

    mapping (string => address) private registry;
    mapping (string => address) private requests;
    address private authority;

    event NewRequest(string hash, address owner);
    event RequestApproved(string hash, address owner);
    event RequestRejected(string hash, address owner);

    constructor() public{
        authority = msg.sender;
    }

    modifier onlyAuthority(){
        require(msg.sender == authority);
        _;
    }
    // Approve a certificate (only by authority)
    function add(string hash, address owner) public onlyAuthority
    {
        registry[hash] = owner;
        emit NewRequest(hash, owner);
    }
    // Revoke a certificate (only by authority)
    function revoke(string hash) public onlyAuthority
    {
        delete(registry[hash]);
    }

    // Check if a certificate is valid
    function verify(string hash) view public returns (bool, address){
        if (registry[hash] == 0x0){
            return (false, 0x0);
        } else {
            return (true, registry[hash]);
        }
    }
    // Request a new certificate is issued
    function request(string hash) public{
        require(registry[hash] == 0x0);
        require(requests[hash] == 0x0);
        requests[hash]=msg.sender;
        emit NewRequest(hash, msg.sender);
    }

    // Approve a certificate request (only by authority)
    function approve(string hash) public onlyAuthority{
        address requester = requests[hash];
        require(requester != 0x0);
        delete(requests[hash]);
        add(hash, requester);
        emit RequestApproved(hash, requester);
    }
    // Reject a certificate request
    function reject(string hash) public onlyAuthority{
        address requester = requests[hash];
        require(requester != 0x0);
        delete (requests[hash]);
        emit RequestRejected(hash, requester);
    }

}
