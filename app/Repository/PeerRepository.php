<?php

namespace App\Repository;

use App\NodePeer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PeerRepository
{
    public function getPeer($host): NodePeer
    {
        $peer = NodePeer::where('host', '=', $host)->first();
        if (!$peer) {
            $peer = new NodePeer(['host' => $host, 'last_activity' => Carbon::now()]);
            $peer->is_new = true;
        }
        return $peer;
    }
    
    /**
     * @return NodePeer[]|Collection
     */
    public function allPeers()
    {
        return NodePeer::all();
    }
    
    public function currentPeer(): NodePeer{
        return new NodePeer(['host' => $_ENV['NODE_HOST']]);
    }
    
    
    /**
     * Get a list of initially known (hardcoded) peers
     * @return NodePeer[]
     */
    public function knownPeers()
    {
        if (!@$_ENV['NODE_PEERS']){
            return [];
        }
        return array_map(function($host){return new NodePeer(['host' => $host]);}, explode(',', @$_ENV['NODE_PEERS']));
    }
    
    public function clearPeers()
    {
        NodePeer::truncate();
    }
}