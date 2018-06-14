<?php

use Faker\Generator as Faker;

$factory->define(App\NodeTransaction::class, function (Faker $faker) {
    return [
        'senderAddress' => 'sender-address',
        'receiverAddress' => 'receiver-address',
        'senderSequence' => 1,
        'sequence' => 1,
        'value' => 3,
        'fee' => 10,
        'data' => 'example data',
        'hash' => 'example-hash'.random_int(1,1000),
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


