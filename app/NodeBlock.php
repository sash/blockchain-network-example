<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class NodeBlock
 *
 * Note: The miner will get the full data of the block it mines. The data will NOT be stored in the database. Once mined
 * the miner will return the full block info (including the transaction hashes) to the node
 *
 * @package App
 *
 * @property int $index
 * @property int $difficulty
 * @property int $cumulativeDifficulty
 * @property string $mined_by_address
 * @property string $previous_block_hash hash of the data above + the hashes of the transactions in sequence
 * @property string $data_hash
 * @property int $nonce
 * @property int $timestamp
 * @property string $block_hash hash(data_hash+nonce+timestamp)
 * @property Collection|NodeTransaction[] $transactions
 *
 */
class NodeBlock extends Model
{
    
    public $chain_id;
    
    protected $fillable = [
            'index',
            'difficulty',
            'cumulativeDifficulty',
            'mined_by_address',
            'previous_block_hash',
            'data_hash',
            'nonce',
            'timestamp',
            'block_hash',
            //TODO why we dont have chain_id in migration? on purpose?
            //'chain_id'
    ];
    
    public function transactions(){
        return $this->hasMany(NodeTransaction::class, 'block_id','id')->orderBy('sequence');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function balances()
    {
        return $this->hasMany(NodeBalance::class, 'block_id');
    }
}
