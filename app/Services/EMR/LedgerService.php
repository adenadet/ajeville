<?php

namespace App\Services\EMR;

use App\Models\EMR\Patient\Patient;
use App\Models\EMR\Patient\Ledger as PatientLedger;
use App\Models\EMR\VisitPayment;
use App\Models\EMR\VisitTransaction;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;

class LedgerService
{

    /**
     * Update patient.balance from all transactions
     */
    public function updatePatientBalance(int $patient_id)
    {
        $totalOutstanding = VisitTransaction::whereHas('visit', function ($q) use ($patient_id) {
                $q->where('patient_id', $patient_id)
                  ->whereIn('status', [1, 2]); // Open or ongoing
            })
            ->where('status', '!=', VisitTransaction::StatusCancelled)
            ->sum('outstanding_amount');

        Patient::where('id', $patient_id)->update(['balance' => $totalOutstanding]);
    }

    public function createLedgerEntry($patient_id, ?int $visit_id, ?int $transaction_id, ?int $payment_id, $type, $direction){
        //Lock Patient 
        $patient = Patient::lockForUpdate()->findOrFail($patient_id);

        // 1 Resolve amount from reference
        if ($payment_id) {
            $reference = VisitPayment::lockForUpdate()->findOrFail($payment_id);
            $amount = $reference->amount;
            $referenceable_type = VisitPayment::class;
            $referenceable_id = $reference->id;
        }

        if ($transaction_id) {
            $reference = VisitTransaction::lockForUpdate()->findOrFail($transaction_id);
            $amount = $reference->coverage ? $reference->coverage->patient_payable : $reference->item_total;
            $referenceable_type = VisitTransaction::class;
            $referenceable_id = $reference->id;
        }

        // 2 Get previous balance (LOCK last row)
        $lastLedger = PatientLedger::where('patient_id', $patient_id)->lockForUpdate()->latest('id')->first();

        $previous_balance = $lastLedger ? $lastLedger->balance_after : 0;

        // 3 Compute new balance_after
        if ($direction === 'debit') {$balance_after = $previous_balance + $amount;} 
        elseif ($direction === 'credit') {$balance_after = $previous_balance - $amount;} 
        else {throw new Exception('Invalid ledger direction');}

        // 4 Create ledger line
        $ledger = PatientLedger::create([
            'patient_id' => $patient_id,
            'visit_id' => $visit_id,
            'visit_transaction_id' => $transaction_id,
            'visit_payment_id' => $payment_id,
            'type' => $type,
            'referenceable_type' => $referenceable_type ?? null,
            'referenceable_id' => $referenceable_id ?? null,
            'amount' => $amount,
            'direction' => $direction == 'debit' ? 'DR' : 'CR',
            'balance_after' => $balance_after,
            'processed' => false,
            'created_by' => auth('api')->id() ?? Auth::id(),
            'updated_by' => auth('api')->id() ?? Auth::id(),
        ]);

        $patient->balance = $balance_after;
        $patient->updated_by = auth('api')->id() ?? Auth::id();
        $patient->save();

        return $ledger;
    }
}
