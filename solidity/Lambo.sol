pragma solidity ^0.4.0;

contract Lambo {
    address private owner;
    mapping (address => uint) private deposits;


    event ByeBitches();
    event NewIdiot(address);

    constructor () public{
        owner = msg.sender;
    }

    modifier onlyOwner(){
        require(owner == msg.sender);
        _;
    }

    function deposit() payable public {
        require(msg.value >= 1 ether); // Are you kiddin' me?! I'm not buying a Lambo with deposits less then an ether!

        deposits[msg.sender] += msg.value;
        emit NewIdiot(msg.sender);
    }

    function checkBalance() view public onlyOwner returns (uint){
        return address(this).balance;
    }

    function bail() public onlyOwner{
        emit ByeBitches();
        selfdestruct(owner);
    }
}
pragma solidity ^0.4.24;

contract MillionDollar {
    mapping(uint => mapping(uint => string)) board;
    mapping(uint => mapping(uint => string)) buyers;
    address private owner = msg.sender;

    struct Buyer {
        address addr;
        uint soldFor;
    }

    event BoardBoxPurchased(uint x, uint y, string char, address buyer);

    function buy(uint x, uint y, string char) payable public {
        require(msg.value >= 1000000000000000);
        require(bytes(board[x][y]).length == 0);
        board[x][y] = char;
        buiers[x][y] = Buyer({addr : msg.sender, soldFor : msg.value});
        emit BoardBoxPurchased(x, y, char, msg.sender);
    }

    function getBoard() view public returns (string){
        return "";
    }
}