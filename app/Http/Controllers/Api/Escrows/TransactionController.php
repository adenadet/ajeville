<?php

namespace App\Http\Controllers\Api\Escrows;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Traits\Escrows\PaymentTrait;
use App\Http\Traits\Escrows\ProductTrait;
use App\Http\Traits\Escrows\TransactionTrait;
use App\Models\Escrows\Transaction;
use App\Models\User;
use App\Notifications\Escrows\Created as EscrowTransactionCreated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class TransactionController extends Controller
{
    use PaymentTrait, ProductTrait, TransactionTrait;

    public function accept(Request $request, string $id)
    {
        $transaction = $this->escrow_transaction_accept($request, $id);
        return response()->json([
            'transaction' => $transaction,
        ], is_string($transaction) ? 500 : 200);
    }

    public function cancel($id)
    {
        $transaction = $this->escrow_transaction_cancel( $id);
        return response()->json([
            'transaction' => $transaction,
            'message' => 'Approved',
        ], is_string($transaction) ? 500 : 200);
    }
    
    public function complete(Request $request, string $id)
    {
        $transaction = $this->escrow_transaction_complete($request, $id);
        return response()->json([
            'transaction' => $transaction,
            'message' => 'Approved',
        ], is_string($transaction) ? 500 : 200);
    }

    public function confirm(Request $request, string $id)
    {
        $transaction = $this->escrow_transaction_confirm($request, $id);
        return response()->json([
            'transaction' => $transaction,
        ], is_string($transaction) ? 500 : 200);
    }
    public function destroy(string $id)
    {
        //
    }

    public function generateReport(Request $request)
    {
        $transactions = $this->escrow_transaction_get_all('my_filtered_transactions', $request, true, false, null);

        // Create unique filename
        $filename = 'transaction_report_' . now()->format('Ymd_His') . '.csv';

        // Define CSV headers
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        // Open output stream
        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');

            // Column headings
            fputcsv($file, [
                'Date',
                'Transaction Code',
                'Buyer',
                'Buyer Email',
                'Amount',
                'Status',
            ]);

            foreach ($transactions as $transaction) {
                
                fputcsv($file, [
                    optional($transaction->created_at)->format('Y-m-d'),
                    $transaction->unique_code,
                    optional($transaction->buyer)->name,
                    optional($transaction->buyer)->email,
                    $transaction->amount,
                    $transaction->status,
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
    
    public function index()
    {
        //echo $_GET['type'];
        return response()->json([
            'transactions' => $this->escrow_transaction_get_all($_GET['type'] ?? 'my_filtered_transactions', $_GET, true, true, $_GET['page'] ?? 1),
            'user' => auth('api')->user() ?? Auth::user(),
        ]);
    }

    public function initials()
    {
        return response()->json([
            'categories' => $this->escrow_item_type_get_all('active', null, false, false, null),
            'user' => auth('api')->user() ?? Auth::user(),
        ]);
    }

    public function notify(string $id)
    {
        $transaction = Transaction::find($id);
        $buyer = User::find($transaction->buyer_id);
        $seller = User::find($transaction->seller_id);
        $user = $transaction->buyer_id == auth('api')->id() ? $seller : $buyer;
        if ($transaction->buyer_id == auth('api')->id()){
            $seller->notify(new EscrowTransactionCreated($transaction, $user));
        }
        else{
            $buyer->notify(new EscrowTransactionCreated($transaction, $user));
        }
        
        return response()->json([
            'transaction' => $this->escrow_transaction_get_by('id', $id,true), 
        ]);
    }

    public function payment(Request $request, string $id)
    {
        $transaction = Transaction::find($id);

        $payment = $this->escrow_transaction_payment_create($request, $transaction, $channel = 'transfer');
        
        return response()->json([
            'payment' => $payment, 
        ], is_string($payment) ? 500 : 201);
    }

    public function quick_transaction(Request $request)
    {
        $transaction = $this->escrow_transaction_quick_create($request);
        
        return response()->json([
            'payment' => $transaction, 
        ], is_string($transaction) ? 500 : 201);
    }

    public function reminder($id)
    {
        $transaction = $this->escrow_transaction_reminder_mail($id);
        
        return response()->json([
            'transaction' => $transaction, 
        ], is_string($transaction) ? 500 : 201);
    }

    public function show($id)
    {
        return response()->json([
            'transaction' => $this->escrow_transaction_get_by($_GET['type'] ?? 'id', $id, true) , 
            'user' => auth('api')->user() ?? Auth::user(),
        ]);
    }

    public function store(Request $request)
    {
        $transaction = $this->escrow_transaction_create($request);
        return response()->json([
            'transaction' => $transaction,
        ], is_string($transaction) ? 500 : 201);
    }

    public function update(Request $request, string $id)
    {
        $transaction = $this->escrow_transaction_update($request, $id);
        return response()->json([
            'transaction' => $transaction,
        ], is_string($transaction) ? 500 : 200);
    }
}