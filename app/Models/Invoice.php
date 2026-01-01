<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number','related_type','related_id','customer_id','sub_total','tax','total','issue_date','due_date','status','lines'
    ];

    protected $casts = [
        'lines' => 'array',
        'issue_date' => 'date',
        'due_date' => 'date'
    ];

    public function related()
    {
        return $this->morphTo();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'related');
    }

    public function getAmountPaidAttribute()
    {
        return (float) $this->transactions()->where('type', 'payment')->sum('amount');
    }

    public function getBalanceAttribute()
    {
        return (float) max(0, $this->total - $this->amount_paid);
    }
}
