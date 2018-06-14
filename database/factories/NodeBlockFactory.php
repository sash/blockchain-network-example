<?php

use Faker\Generator as Faker;

$factory->define(App\NodeBlock::class, function (Faker $faker) {
    return [
        'index' => 1,
        'difficulty' => 4,
        'mined_by_address' => 'mined-by-address',
        'previous_block_hash' => 'previous-block-hash',
        'data_hash' => 'data-hash'.random_int(1,1000),
        'nonce' => 1000,
        'timestamp' => time(),
        'block_hash' => 'block-hash'.random_int(1,1000),
        'cumulativeDifficulty' => 1000
    ];
});
