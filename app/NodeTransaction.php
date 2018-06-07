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
 * @property string $data
 * @property string $hash
 * @property string $senderPublicKey
 * @property string $signature
 * @property boolean|null $transferSuccessful
 * @property int|null $minedInBlockIndex
 * @property Carbon $created_at
 */
class NodeTransaction extends Model
{

}
