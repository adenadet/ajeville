<?php

namespace App\Http\Controllers\Api\Escrows;

use App\Http\Controllers\Controller;
use App\Http\Traits\Escrows\PaymentTrait;
use App\Http\Traits\Escrows\TransactionTrait;
use App\Models\Escrows\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class PaymentController extends Controller
{
    use PaymentTrait, TransactionTrait;

    public function filter(Request $request)
    {
        $payments = $this->escrow_payments_get_all('filtered', $request, true, true, $_GET['page'] ?? 1);
        
        return response()->json([
            'payments' => $payments,
            'user' => auth('api')->user() ?? Auth::user(), 
        ], is_string($payments) ? 500 : 200);
    }


    public function generateReport(Request $request)
    {
        // Fetch filtered transactions - add your filtering logic if needed
        $payments = $this->escrow_payments_get_all('my_payments', $request, true, false, null);

        // Create unique filename
        $filename = 'payment_report_' . now()->format('Ymd_His') . '.csv';

        // Define CSV headers
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        // Open output stream
        $callback = function () use ($payments) {
            $file = fopen('php://output', 'w');

            // Column headings
            fputcsv($file, [
                'Payment Date',
                'Transaction Code',
                'Paid By',
                'Payee Email',
                'Amount',
                'Payment Channel',
                'Payment ID',
            ]);

            foreach ($payments as $payment) {
                $transaction = $payment->transaction;

                //if (!$payment) continue;

                fputcsv($file, [
                    optional($payment->time_stamped ?? $payment->transaction->created_at)->format('Y-m-d H:i:s'),
                    $payment->description,
                    optional($transaction->buyer)->name ?? optional($transaction->buyer)->first_name . ' ' . optional($transaction->buyer)->last_name,
                    optional($transaction->buyer)->email,
                    $payment->amount ?? $transaction->amount,
                    ucfirst($payment->channel) ?? '',
                    $transaction->unique_code,
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function index()
    {
        $payments = $this->escrow_payments_get_all($_GET['type'] ?? 'my_payments', $_GET, true, true, $_GET['page'] ?? 1);
        
        return response()->json([
            'payments' => $payments,
            'user' => auth('api')->user() ?? Auth::user(), 
        ], is_string($payments) ? 500 : 200);
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
