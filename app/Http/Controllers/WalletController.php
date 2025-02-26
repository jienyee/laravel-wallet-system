<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\Transaction;
use App\Jobs\CalculateRebate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function deposit(Request $request, $userId)
    {
        $amount = $request->amount;

        DB::transaction(function () use ($userId, $amount) {
            $wallet = Wallet::lockForUpdate()->firstOrCreate(
                ['user_id' => $userId],
                ['balance' => 0]
            );

            $wallet->balance += $amount;
            $wallet->save();

            Transaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'deposit',
                'amount' => $amount
            ]);

            CalculateRebate::dispatch($wallet, $amount);
        });

        return response()->json(['message' => 'Deposit successful.']);
    }

    public function withdraw(Request $request, $userId)
    {
        $amount = $request->amount;

        DB::transaction(function () use ($userId, $amount) {
            $wallet = Wallet::lockForUpdate()->where('user_id', $userId)->firstOrFail();

            if ($wallet->balance < $amount) {
                abort(400, 'Insufficient balance.');
            }

            $wallet->balance -= $amount;
            $wallet->save();

            Transaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'withdrawal',
                'amount' => $amount
            ]);
        });

        return response()->json(['message' => 'Withdrawal successful.']);
    }

    public function balance($userId)
    {
        $wallet = Wallet::where('user_id', $userId)->firstOrFail();
        return response()->json(['balance' => $wallet->balance]);
    }

    public function transactions($userId)
    {
        $wallet = Wallet::where('user_id', $userId)->firstOrFail();
        return response()->json(
            $wallet->transactions()->orderBy('created_at', 'desc')->get()
        );
    }
}