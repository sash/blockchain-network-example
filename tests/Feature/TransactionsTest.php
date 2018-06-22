<?php

namespace Tests\Feature;

use App\Crypto\PublicPrivateKeyPair;
use App\Crypto\TransactionSigner;
use App\NodeTransaction;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TransactionsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param array $overrides
     */
    private function requestParams($overrides = [])
    {
        $sender = PublicPrivateKeyPair::generate();
        $receiver = PublicPrivateKeyPair::generate();

        $transaction = array_merge([
            'from' => $sender->getAddress(),
            'from_id' => 1,
            'to' => $receiver->getAddress(),
            'value' => 200,
            'fee' => 10,
            'timestamp' => Carbon::now()->subYear(1)->timestamp,
            'hash' => str_repeat('s0mehash',8),
            'signature' => str_repeat('s0mes1gnature',10)
        ],$overrides);

        return ['transaction' => $transaction];
    }

    /** @test */
    public function transaction_can_be_posted()
    {
        $transaction = $this->buildTransaction();

        $response = $this->postJson('/api/transaction', ['transaction' => $transaction]);

        $response->assertStatus(201);
        
        $this->assertCount(1, NodeTransaction::all());

        tap(NodeTransaction::first(), function($tx) use ($transaction){
            $this->assertEquals($transaction['from'], $tx->senderAddress);
            $this->assertEquals($transaction['from_id'], $tx->senderSequence);
            $this->assertEquals($transaction['to'], $tx->receiverAddress);
            $this->assertEquals($transaction['value'], $tx->value);
            $this->assertEquals($transaction['fee'], $tx->fee);
            $this->assertEquals($transaction['data'], $tx->data);
            $this->assertEquals($transaction['hash'], $tx->hash);
            $this->assertEquals($transaction['signature'], $tx->signature);
        });
    }

    /** @test */
    public function from_address_is_required()
    {
        $response = $this->postJson('/api/transaction',$this->requestParams([
            'from' => ''
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('from');
    }

    /** @test */
    public function from_address_must_be_alpha_numeric()
    {
        $response = $this->postJson('/api/transaction',$this->requestParams([
            'from' => '~must-be-alpha-numeric.'
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('from');
    }

    /** @test */
    public function from_address_must_be_with_length_of_40_characters()
    {
        $response = $this->postJson('/api/transaction',$this->requestParams([
            'from' => 'thisIsAnAlphaNumericString123'
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('from');
    }

    /** @test */
    public function from_id_is_required()
    {
        $response = $this->postJson('/api/transaction',$this->requestParams([
            'from_id' => ''
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('from_id');
    }

    /** @test */
    public function to_is_required()
    {
        $response = $this->postJson('/api/transaction',$this->requestParams([
            'to' => ''
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('to');
    }

    /** @test */
    public function to_address_must_be_alpha_numeric()
    {
        $response = $this->postJson('/api/transaction',$this->requestParams([
            'to' => '~must-be-alpha-numeric.'
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('to');
    }

    /** @test */
    public function to_address_must_be_with_length_of_40_characters()
    {
        $response = $this->postJson('/api/transaction',$this->requestParams([
            'from' => 'thisIsAnAlphaNumericString123'
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('from');
    }

    /** @test */
    public function value_is_required()
    {
        $response = $this->postJson('/api/transaction',$this->requestParams([
            'value' => ''
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('value');
    }

    /** @test */
    public function value_must_be_an_integer()
    {
        $response = $this->postJson('/api/transaction',$this->requestParams([
            'value' => 1.5
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('value');
    }

    /** @test */
    public function value_must_be_greater_than_zero()
    {
        $response = $this->postJson('/api/transaction',$this->requestParams([
            'value' => -1
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('value');
    }

    /** @test */
    public function fee_is_required()
    {
        $response = $this->postJson('/api/transaction',$this->requestParams([
            'fee' => ''
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('fee');
    }

    /** @test */
    public function fee_must_be_an_integer()
    {
        $response = $this->postJson('/api/transaction',$this->requestParams([
            'fee' => 1.5
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('fee');
    }

    /** @test */
    public function hash_is_required()
    {
        $response = $this->postJson('/api/transaction',$this->requestParams([
            'hash' => ''
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('hash');
    }

    /** @test */
    public function hash_must_be_alpha_numeric()
    {
        $response = $this->postJson('/api/transaction',$this->requestParams([
            'hash' => '~alpha-numeric.'
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('hash');
    }

    /** @test */
    public function hash_must_be_64_characters_long()
    {
        $response = $this->postJson('/api/transaction',$this->requestParams([
            'hash' => 'not64characterlonghash'
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('hash');
    }

    /** @test */
    public function hash_must_be_unique()
    {
        factory(NodeTransaction::class)->create([
            'hash' => 'unique-hash'
        ]);

        $response = $this->postJson('/api/transaction',$this->requestParams([
            'hash' => 'unique-hash'
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('hash');
    }

    /** @test */
    public function signature_is_required()
    {
        $response = $this->postJson('/api/transaction',$this->requestParams([
            'signature' => ''
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('signature');
    }

    /** @test */
    public function signature_must_be_130_characters_long()
    {
        $response = $this->postJson('/api/transaction',$this->requestParams([
            'signature' => 'not130characterslong'
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('signature');
    }

    /** @test */
    public function signature_must_be_alpha_numeric()
    {
        $response = $this->postJson('/api/transaction',$this->requestParams([
            'signature' => '~must-be-alpha-numeric.'
        ]));

        $response->assertStatus(422);
        $response->assertJsonHasErrors('signature');
    }
}
