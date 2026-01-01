<?php

namespace App\Services\Finance;

use App\Models\Finance\Income;
use App\Models\Finance\Payment;
use App\Models\Finance\PaymentAllocation;
use Illuminate\Support\Facades\DB;

class PaymentAllocationService
{
    public function allocateIncome(Income $income)
    {
        return DB::transaction(function () use ($income) {
            $remainingIncomeAmount = $income->amount;
            $customerId = $income->customer_id;

            // 1. Get customer payments with unallocated balance
            $payments = Payment::where('customer_id', $customerId)
                ->get()
                ->filter(function ($payment) {
                    $allocated = $payment->allocations()->sum('amount');
                    return $payment->amount > $allocated;
                });

            // 2. Loop through each payment and allocate
            foreach ($payments as $payment) {

                if ($remainingIncomeAmount <= 0) break;

                $allocated = $payment->allocations()->sum('amount');
                $paymentBalance = $payment->amount - $allocated;

                if ($paymentBalance <= 0) continue;

                // Determine how much to allocate from this income
                $allocateAmount = min($remainingIncomeAmount, $paymentBalance);

                PaymentAllocation::create([
                    'date'       => date('Y-m-d'),   
                    'payment_id' => $payment->id,
                    'income_id'  => $income->id,
                    'wallet_id'  => null,
                    'amount'     => $allocateAmount,
                ]);

                $remainingIncomeAmount -= $allocateAmount;
            }

            // 3. If still remaining, store in customer wallet
            if ($remainingIncomeAmount > 0) {
                PaymentAllocation::create([
                    'date'       => date('Y-m-d'),    
                    'payment_id' => null,
                    'income_id'  => $income->id,
                    'wallet_id'  => $customerId,
                    'amount'     => $remainingIncomeAmount,
                ]);
            }

            return true;
        });
    }

    public function allocatePayment(Payment $payment)
    {
        return DB::transaction(function () use ($payment) {

            $remainingPaymentAmount = $payment->amount;
            $customerId = $payment->customer_id;

            // 1. Get all incomes for this customer that are underallocated
            $incomes = Income::where('customer_id', $customerId)->orderBy('id')->get()
                ->filter(function ($income) {
                    $allocated = $income->allocations()->whereNotNull('payment_id')->sum('amount');
                    return $income->amount > $allocated; // underallocated invoice
                });

            // 2. Allocate payment to each underallocated income
            foreach ($incomes as $income) {
                if ($remainingPaymentAmount <= 0) break;
                $allocated = $income->allocations()->whereNotNull('payment_id')->sum('amount');
                $incomeBalance = $income->amount - $allocated;
                if ($incomeBalance <= 0) continue;
                // Determine how much from this payment should be allocated
                $allocateAmount = min($remainingPaymentAmount, $incomeBalance);

                PaymentAllocation::create([
                    'payment_id' => $payment->id,
                    'income_id'  => $income->id,
                    'wallet_id'  => null,
                    'amount'     => $allocateAmount,
                    'date'       => date('Y-m-d'),
                ]);
                $remainingPaymentAmount -= $allocateAmount;
            }
            return true;
        });
    }
}
