<?php

namespace App\Jobs;

use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CalculateRebate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $wallet, $amount;

    public function __construct(Wallet $wallet, $amount)
    {
        $this->wallet = $wallet;
        $this->amount = $amount;
    }

    public function handle()
    {
        DB::transaction(function () {
            $rebate = $this->amount * 0.01;
            $this->wallet->refresh();
            $this->wallet->balance += $rebate;
            $this->wallet->save();

            Transaction::create([
                'wallet_id' => $this->wallet->id,
                'type' => 'rebate',
                'amount' => $rebate
            ]);
        });
    }
}