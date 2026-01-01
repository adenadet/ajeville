<?php

namespace App\Traits;

use App\Models\Finance\Journal;
use App\Models\Finance\Receivable;
use App\Models\Finance\Payable;
use Illuminate\Support\Facades\DB;

trait JournalTrait
{
    public function finance_journal_create_journal_entry(array $data)
    {
        return DB::transaction(function () use ($data) {
            return Journal::create($data);
        });
    }

    public function finance_journal_get_all($type, $specific, $detailed, $paginated, $page)
    {
        $query = Journal::query();

        switch($type){
            case 'all':
                $query->withTrashed();
            break;
            case 'completed':
                $query->where('status', '=', Journal::StatusCompleted);
            break;
            case 'contact_center':
                $query->where('contact_center_id', '=', $specific['contact_centre_id']);
            break;
        }
        $query->orderBy('date', 'DESC');
        $query = $paginated ? $query->paginate(50) : $query->get();
        return $query;
    }

    public function finance_journal_update_journal_entry($data, $id)
    {
        $journal = Journal::findOrFail($id);
        $journal->update($data);
        return $journal;
    }

    public function deleteJournalEntry(int $id)
    {
        $journal = Journal::findOrFail($id);
        return $journal->delete();
    }

    public function getJournalEntries($filters = [])
    {
        $query = Journal::query();

        if (isset($filters['cost_center_id'])) {
            $query->where('cost_center_id', $filters['cost_center_id']);
        }

        if (isset($filters['transaction_type_id'])) {
            $query->where('transaction_type_id', $filters['transaction_type_id']);
        }

        if (isset($filters['date_range'])) {
            $query->whereBetween('journal_date', $filters['date_range']);
        }

        return $query->latest()->get();
    }

    public function recordReceivable(array $data)
    {
        $receivable = Receivable::create($data);
        $this->createJournalEntry([
            'description' => 'Receivable Recorded',
            'amount' => $data['amount'],
            'transaction_type_id' => $data['transaction_type_id'],
            'cost_center_id' => $data['cost_center_id'],
            'currency_id' => $data['currency_id'],
            'journal_date' => now(),
            'reference_id' => $receivable->id,
            'reference_type' => Receivable::class,
        ]);
        return $receivable;
    }

    public function recordPayable(array $data)
    {
        $payable = Payable::create($data);
        $this->createJournalEntry([
            'description' => 'Payable Recorded',
            'amount' => $data['amount'],
            'transaction_type_id' => $data['transaction_type_id'],
            'cost_center_id' => $data['cost_center_id'],
            'currency_id' => $data['currency_id'],
            'journal_date' => now(),
            'reference_id' => $payable->id,
            'reference_type' => Payable::class,
        ]);
        return $payable;
    }

    public function linkJournalToOrder($order, $description = 'Linked Order Journal')
    {
        return $this->createJournalEntry([
            'description' => $description,
            'amount' => $order->amount,
            'transaction_type_id' => $order->transaction_type_id,
            'cost_center_id' => $order->cost_center_id,
            'currency_id' => $order->currency_id,
            'journal_date' => now(),
            'reference_id' => $order->id,
            'reference_type' => get_class($order),
        ]);
    }
}
