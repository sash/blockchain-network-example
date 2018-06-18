<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class NodeTransaction
 *
 * @package App
 *
 * @property string $senderAddress
 * @property string $receiverAddress
 * @property int $value
 * @property int $fee
 * @property string $data
 * @property string $hash
 * @property string $signature
 * @property int|null $block_id
 * @property int $timestamp
 * @property int $senderSequence
 * @property int|null $sequence
 * @property NodeBlock|null $block
 * @property bool $isCoinbase
 *
 * @method \Illuminate\Database\Eloquent\Builder withConfirmations(int $confirmations, int $topBlockIndex)
 * @method \Illuminate\Database\Eloquent\Builder coinbase()
 */
class NodeTransaction extends Model
{
    public $timestamps = false;
    const COINBASE_ADDRESS = '0000000000000000000000000000000000000000';

    protected $fillable = [
        'senderAddress',
        'senderSequence',
        'receiverAddress',
        'sequence',
        'value',
        'fee',
        'data',
        'hash',
        'signature',
        'timestamp'
    ];

    /**
     * Scope a query to only include active users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $confirmations
     * @param int $topBlockIndex
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithConfirmations($query, $confirmations, $topBlockIndex)
    {
        return $query
                ->leftJoin('node_blocks', 'node_transactions.block_id', '=', 'node_blocks.id')
                ->where('node_blocks.index','<=', $topBlockIndex - $confirmations + 1)
                ->whereNotNull('node_transactions.block_id');
    }
    
    public function scopeCoinbase($query){
        return $query->where('senderAddress', '=', self::COINBASE_ADDRESS);
    }
    
    public function block(){
        return $this->belongsTo(NodeBlock::class, 'block_id');
    }
    
    public function attributeIsCoinbase(){
        return $this->senderAddress == self::COINBASE_ADDRESS;
    }
}

