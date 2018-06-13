<?php

use Faker\Generator as Faker;

$factory->define(App\NodeTransaction::class, function (Faker $faker) {
    return [
        'senderAddress' => 'sender-address',
        'senderSequence' => 1,
        'sequence' => 1,
        'value' => 3,
        'fee' => 1,
        'data' => 'example data',
        'hash' => 'example-hash',
        'signature' => 'example-signature',
    ];
});

$factory->state(App\NodeTransaction::class, 'pending', function () {
    return [
        'block_id' => null,
    ];
});

$factory->state(App\NodeTransaction::class, 'confirmed', function () {
    return [
        'block_id' => random_int(1,100),
    ];
});


