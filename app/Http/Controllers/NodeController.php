<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Class NodeController
 * @package App\Http\Controllers
 *
 * API:
 *  - GET /blocks list all blocks. Used by other nodes to sync
 *  - GET /block/{:blockIndex}. Get Index of the block
 *  -
 *  0
 */
class NodeController extends Controller
{
    public function __construct()
    {
//        $this->middleware('guest');
    }
    
    function getBlocks(){
        return ['foo'];
    }
}
