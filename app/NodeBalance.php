<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class NodeBalance
 * @package App
 *
 * @property string $block_id
 * @property string $address
 * @property int $balance
 * @property NodeBlock $block
 *
 * @method Builder forAddress(string $address)
 */
class NodeBalance extends Model
{
    protected $fillable = ['address', 'balance'];
    
    public $timestamps = false;
    
    public function scopeForAddress($query, $address)
    {
        return $query->where('address', '=', $address);
    }
    
    public function block()
    {
        return $this->belongsTo(NodeBlock::class, 'block_id');
    }
}
