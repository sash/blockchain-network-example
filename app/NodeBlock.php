<?php

namespace App;

use App\Crypto\BlockHasher;
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
 * @property string $mined_by_address
 * @property string $previous_block_hash hash of the data above + the hashes of the transactions in sequence
 * @property string $data_hash
 * @property int $nonce
 * @property int $timestamp
 * @property string $block_hash hash(data_hash+nonce+timestamp)
 * @property Collection|NodeTransaction[] $transactions
 *
 * @method static \Illuminate\Database\Eloquent\Builder mined()
 */
class NodeBlock extends Model
{
    
    protected $fillable = [
            'index',
            'difficulty',
            'mined_by_address',
            'previous_block_hash',
            'data_hash',
            'nonce',
            'timestamp',
            'block_hash',
    ];
    public function transactions(){
        return $this->hasMany(NodeTransaction::class, 'block_id')->orderBy('sequence');
    }
    
}
