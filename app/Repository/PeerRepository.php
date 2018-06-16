<?php

namespace App\Repository;

use App\NodePeer;
use Carbon\Carbon;

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
     * @return NodePeer[]
     */
    public function allPeers()
    {
        return NodePeer::all()->get();
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
        if (!@$_ENV['NODE_HOST']){
            return [];
        }
        return array_map(function($host){return new NodePeer(['host' => $host]);}, explode(',', @$_ENV['NODE_HOST']));
    }
    
    public function clearPeers()
    {
        NodePeer::all()->delete();
    }
}