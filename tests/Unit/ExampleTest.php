<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function deposit_with_rebate()
    {
        dump('Test 1: Deposit with Rebate');
        $this->postJson('/wallet/deposit/1', ['amount' => 100]);

        $wallet = Wallet::where('user_id', 1)->firstOrFail();
        dump('Wallet balance:', $wallet->balance);
        $this->assertEquals(101, $wallet->balance);
        dump('Test 1 passed!');
    }

    #[Test]
    public function concurrent_deposits_with_rebate()
    {
        dump('Test 2: Concurrent Deposits with Rebate');
        $this->postJson('/wallet/deposit/1', ['amount' => 100]);
        $this->postJson('/wallet/deposit/1', ['amount' => 200]);

        $wallet = Wallet::where('user_id', 1)->firstOrFail();
        dump('Wallet balance:', $wallet->balance);
        $this->assertEquals(303, $wallet->balance);
        dump('Test 2 passed!');
    }
}
