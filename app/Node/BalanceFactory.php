<?php

namespace App\Node;

use App\NodeBlock;
use App\Repository\BalanceRepository;

class BalanceFactory
{
    /**
     * @var BalanceRepository
     */
    private $repository;
    
    /**
     * BalanceFactory constructor.
     * @param BalanceRepository $repository
     */
    function __construct(BalanceRepository $repository)
    {
        $this->repository = $repository;
    }
    
    /**
     * Balance based on a confirmed block (saved in the database)
     * @param NodeBlock $block
     */
    public function forCurrentBlock(NodeBlock $block){
        $balance = $this->repository->getBalanceForBlock($block);
        return new Balance($balance, $this->repository);
    }
    
    /**
     * Balance constructor that include all accepted valid pending transactions (saved in the database)
     * @deprecated All incoming transactions must be declared valid based on balance so this is not used
     */
    public function forCurrentPending(){
        $balance = $this->repository->getBalanceForPending();
        return new Balance($balance, $this->repository);
    }
}