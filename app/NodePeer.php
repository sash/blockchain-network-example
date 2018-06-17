<?php

namespace App;

use App\Node\PeerClient;
use App\Node\PeerCommunicationTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class NodePeer
 * @package App
 *
 * @property string $host
 * @property Carbon $last_activity
 * @property PeerClient $client
 */
class NodePeer extends Model
{
    
    public $is_new = false; // Check of the peer was recently added to the database
    
    protected $dates = ['last_activity'];
    
    protected $fillable = ['host', 'last_activity'];
    
    public $timestamps = false;
    
    public function wasActive(){
        $this->last_activity = Carbon::now();
        $this->save();
        return $this;
    }
    
    public function getClientAttribute(){
        return new PeerClient($this);
    }
    
}
