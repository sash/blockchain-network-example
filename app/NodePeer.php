<?php

namespace App;

use App\Node\PeerCommunicationTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class NodePeer
 * @package App
 *
 * @property string $host
 * @property Carbon $last_activity
 */
class NodePeer extends Model
{
    use PeerCommunicationTrait;
    
    public $is_new = false; // Check of the peer was recently added to the database
    
    protected $fillable = ['host', 'last_activity'];
    
    public $timestamps = false;
    
    public function wasActive(){
        $this->last_activity = Carbon::now();
        $this->save();
        return $this;
    }
    
}
