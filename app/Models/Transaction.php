<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public $timestamps = false;
    protected $fillable = ['wallet_id', 'type', 'amount', 'created_at'];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
