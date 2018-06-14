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
 * @property string $senderPublicKey
 * @property string $signature
 * @property boolean|null $transferSuccessful
 * @property int|null $block_id
 * @property Carbon $created_at
 * @property int $senderSequence
 * @property int $sequence
 * @property NodeBlock|null $block
 *
 * @method static \Illuminate\Database\Eloquent\Builder withConfirmations(int $confirmations, int $topBlockIndex)
 */
class NodeTransaction extends Model
{
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
                ->whereNotNull('block_id');
    }
    
    public function block(){
        return $this->belongsTo(NodeBlock::class, 'block_id');
    }
}
