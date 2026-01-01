<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'type','amount','reference','bank_account_id','related_type','related_id','transaction_date','meta'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'meta' => 'array'
    ];

    public function related()
    {
        return $this->morphTo();
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }
}
