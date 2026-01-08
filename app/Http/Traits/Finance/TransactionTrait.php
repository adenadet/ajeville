<?php
namespace App\Http\Traits\Finance;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

trait TransactionTrait
{
    public function finance_transaction_create($item, $patient_id, $quantity, $done, $visit_id, $plan_id){

    }

    public function createTransaction(string $type, float $amount, array $data = [])
    {
        $tx = Transaction::create([
            'type' => $type,
            'amount' => $amount,
            'reference' => $data['reference'] ?? (Str::upper(Str::random(10))),
            'bank_account_id' => $data['bank_account_id'] ?? null,
            'related_type' => $data['related_type'] ?? ($data['related'] ?? null),
            'related_id' => $data['related_id'] ?? ($data['related_id'] ?? null),
            'transaction_date' => $data['transaction_date'] ?? Carbon::now()->toDateString(),
            'meta' => isset($data['meta']) ? json_encode($data['meta']) : null,
        ]);

        return $tx;
    }

    public function sumTransactions(string $type, array $opts = [])
    {
        $q = Transaction::where('type', $type);
        if (!empty($opts['date_from'])) $q->whereDate('transaction_date', '>=', $opts['date_from']);
        if (!empty($opts['date_to'])) $q->whereDate('transaction_date', '<=', $opts['date_to']);
        return (float) $q->sum('amount');
    }

    public function reconcileLine(array $line)
    {
        // try exact reference match first
        if (!empty($line['reference'])) {
            $tx = Transaction::where('reference', $line['reference'])->first();
            if ($tx) return $tx;
        }

        // fallback: match by amount and date (within 2 days)
        if (!empty($line['amount'])) {
            $date = $line['date'] ?? null;
            $q = Transaction::where('amount', $line['amount']);
            if ($date) {
                $q->whereBetween('transaction_date', [
                    \Carbon\Carbon::parse($date)->subDays(2)->toDateString(),
                    \Carbon\Carbon::parse($date)->addDays(2)->toDateString(),
                ]);
            }
            return $q->first();
        }

        return null;
    }
}
