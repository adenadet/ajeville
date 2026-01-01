<?php
namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Http\Traits\Finance\TransactionTrait;

class ReconciliationController extends Controller
{
    use TransactionTrait;

    /**
     * Import bank statement CSV (simple parser) and attempt to match lines to transactions.
     * Expected CSV columns (header): date,description,reference,amount
     */
    public function importCsv(Request $req)
    {
        $req->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $path = $req->file('file')->getRealPath();
        $contents = file_get_contents($path);
        $lines = array_map('trim', preg_split('/\r?\n/', $contents));
        $header = null;
        $results = ['matched' => [], 'unmatched' => []];

        foreach ($lines as $idx => $line) {
            if (empty($line)) continue;
            $parts = str_getcsv($line);
            if ($idx === 0) {
                $header = array_map('strtolower', array_map('trim', $parts));
                continue;
            }
            $row = array_combine($header, $parts);
            $lineObj = [
                'reference' => $row['reference'] ?? null,
                'amount' => isset($row['amount']) ? floatval(str_replace([',',' '],'',$row['amount'])) : null,
                'date' => $row['date'] ?? null,
                'description' => $row['description'] ?? null
            ];
            $match = $this->reconcileLine($lineObj);
            if ($match) {
                $results['matched'][] = [
                    'line' => $lineObj,
                    'transaction_id' => $match->id
                ];
            } else {
                $results['unmatched'][] = $lineObj;
            }
        }

        return response()->json($results);
    }
}
