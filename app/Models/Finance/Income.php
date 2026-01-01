<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Income extends Model
{
    use HasFactory;

    const StatusUnconfirmed = 1;
    const StatusConfirmed = 10;
    const StatusQueried = 5;
    const StatusRejected = 40;
    const StatusDeleted = 100;
    const StatusPaid = 400;
    const StatusPartPaid = 300;
    
    protected $primaryKey = 'id';

    protected $table = 'finance_incomes';
    protected $fillable = ['unique_id', 'date', 'due_date', 'branch_id', 'classification_id', 'income_name', 'incomeable_type', 'incomeable_id', 'account_id', 'amount', 'payable', 'vendor_id', 'customer_id', 'staff_id', 'description', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'
    ];

    // Relationships
    public function account()
    {
        //Know which account was debited for this transaction
        return $this->belongsTo("App\Models\Finance\Account");
    }

    public function allocations()
    {
        return $this->hasMany('App\Models\Finance\PaymentAllocation', 'income_id', 'id');
    }

    public function wallet_debits()
    {
        //Where money was taken from customer's balance directly
        return $this->hasMany('App\Models\Finance\WalletDebit', 'income_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo("App\Models\User", 'created_by', 'id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\CRM\Customer', 'customer_id', 'id');
    }

    public function deleter()
    {
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function incomeable()
    {
        return $this->morphTo();
    }

    public function classification()
    {
        return $this->belongsTo('App\Models\Finance\ExpenseType', 'classification_id', 'id');
    }

    public function mainTransaction()
    {
        return $this->morphOne("\App\Models\Finance\MainTransaction", 'transactionable');
    }

    public function staff()
    {
        return $this->belongsTo('App\Models\User', 'staff_id', 'id');
    }

    public function updater()
    {
        return $this->belongsTo("App\Models\User", 'updated_by', 'id');
    }

    public function vendor()
    {
        return $this->belongsTo('App\Models\Procurement\Vendor', 'vendor_id', 'id');
    }

    public function computeTotalPayments(): float
    {
        $allocTable = 'finance_payment_allocations';
        $paymentsTable = 'finance_wallet_debits';
        $incomeId = $this->id;

        // CASE A: allocation/pivot rows exist for this income
        $allocExists = DB::table($allocTable)
            ->where('income_id', $incomeId)
            ->exists();

        if ($allocExists) {
            return (float) DB::table($allocTable . ' as a')
                ->join($paymentsTable . ' as p', 'a.pay_out_id', '=', 'p.id')
                ->where('a.income_id', $incomeId)
                ->whereNull('a.deleted_at') // skip soft-deleted allocations
                ->whereNull('p.deleted_at') // skip soft-deleted payments
                ->sum(DB::raw('ROUND(amount, 2)'));
        }

        // CASE B: no allocations — fallback to payments.invoice_id
        // Only count confirmed payments (and not soft-deleted)
        $sum = DB::table($paymentsTable)
            ->where('income_id', $incomeId)
            ->whereNull('deleted_at')
            ->sum(DB::raw('COALESCE(amount,0)'));

        return (float) $sum;
    }

    public function getTotalPaymentsAttribute(): float
    {
        // Keep it simple for accessor; avoid infinite recursion
        return $this->computeTotalPayments();
    }

    public function getBalanceAttribute(): float
    {
        $amount = (float) $this->amount;
        $paid = (float) $this->computeTotalPayments();

        $balance = $amount - $paid;

        return $balance > 0 ? $balance : 0.00;
    }

    /**
     * Optional helper: get outstanding as signed (can be negative to indicate overpayment)
     * Use this if you want to show negative balance (credit)
     */
    public function outstandingSigned(): float
    {
        return (float) $this->amount - (float) $this->computeTotalPayments();
    }
}
