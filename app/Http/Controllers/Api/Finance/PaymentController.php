<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Traits\CRM\CustomerTrait;
use App\Http\Traits\Finance\IncomeTrait;
use App\Http\Traits\Finance\TransactionTrait; 
use App\Http\Traits\Sales\OrderTrait;

use App\Models\Finance\Deposit;
use App\Models\CRM\Customer;
use App\Models\Finance\BranchBank;
use App\Models\Finance\PaymentMode;
use App\Models\Finance\Transaction;
use App\Models\Payment;
use App\Models\EMR\Patient;
use App\Models\Sales\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Services\Finance\MainService;

class PaymentController extends Controller
{
    use CustomerTrait, IncomeTrait, TransactionTrait;
    //use MainService;
    public function confirm($id)
    {
        //This should retract the payment as a payment affects many things in the system 
        $payment = $this->finance_payment_confirm($id);

        return response()->json(['payment' => $payment], is_string($payment) ? 500 : 200);
    }

    public function destroy($id)
    {
        //This should retract the payment as a payment affects many things in the system 
        $payment = $this->finance_payment_deactivate($id);

        return response()->json([
            'message' => 'Payment deleted successfully',
            'payment' => $payment,
        ],is_string($payment) ? 500 : 200);
    }

    // Display a list of payments
    public function index()
    {
        return response()->json([
            'payments' => $this->finance_payment_get_all($_GET['status'] ?? 'all', $_GET, true, true, $_GET['page']),
        ]);
    }

    public function initials()
    {
        return response()->json([
            'banks' => BranchBank::where('status', '=', 1)->with(['bank'])->orderBy('account_name', 'ASC')->get(),
            'customers' => $this->crm_customer_get_all('active', null, false, false, null),
            'modes' => PaymentMode::where('status', '=', 1)->orderBy('name', 'ASC')->get(),
        ]);
    }

    // Show a single payment
    public function show($id)
    {
        $payment = $this->finance_payment_get_by($id, true);
        
        return response()->json([
            'payment' => $payment,
        ], is_string($payment) ? 404 : 200);
    }

    // Create a new deposit and associated payments
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'sometimes|date',
            'bank_id' => 'sometimes|exists:finance_branch_banks,id',
            'customer_id' => 'nullable|exists:crm_customers,id',
            'vendor_id' => 'nullable|exists:procurement_vendors,id',
            'staff_id' => 'nullable|exists:hrms_employees,id',
            'mode_id' => 'required|exists:finance_payment_modes,id',
            'amount' => 'required|numeric|min:1',
            'collected_by' => 'sometimes|numeric',
            'collected_at' => 'sometimes|date',
            'reference_id' => 'nullable|numeric',
            'transactions' => 'nullable|array',
            'transactions.*' => 'sometimes|exists:finance_transactions,id',
            'trans_type' => 'sometimes|string',
        ]);

        $payment = $this->finance_payment_create($request);

        return response()->json([
            'payment' => $payment,
        ], is_string($payment) ? 500 : 201);
        /*DB::beginTransaction();

        try {
            // Create the deposit
            $deposit = $this->finance_payment_deposit_create($validated);

            // Update the customer's balance
            if (isset($validated['customer_id']) && !is_null($validated['customer_id']) &&($validated['customer_id'] != 0)){
                $customer = $this->crm_customer_update_balance($validated['customer_id'], $validated['amount']);
            }
            
            $totalPayment = 0;

            switch ($validated['trans_type']){
                case 'sales':
                    $order = $this->sales_order_get_by(null, $validated['order_id'], true);
                    //echo $order->total_cost;
                    if (isset($customer)){
                        if($order->total_cost <= $customer->balance){
                            $order->payment_status = Order::PaymentStatusPaid;
                            $order->status = Order::StatusPaid;
                            $order->updated_by = auth('api')->id() ?? Auth::id();
                            $order->save();
                        }
                    }
                    echo $order->total_cost."\n";
                    $sum = 0;
                    foreach ($order->order_items as $item) {
                        echo '/n'.$item->item_id.'['.$item->unit_price * $item->quantity.'] \n';
                        $sum += ($item->unit_price * $item->quantity); 
                        //$totalPayment += ($item->unit_price * $item->fulfilled_quantity);
                    }
                    echo "Logistics: ".$order->logistics."\n";
                    echo "Discount: ".$order->discount."\n";
                    echo "Taxes: ".(0.075 * $sum)."\n"; 
                    if ($order->total_cost > $validated['amount']) {
                        throw new \Exception("Insufficient amount for order ID: {$validated['order_id']}");
                    }
                break;
                case 'transaction':
                break;
                case 'transactions':
                break;
            }

            /*
            if (!empty($validated['transactions'])) {
                foreach ($validated['transactions'] as $transaction_id) {
                    $transaction = Transaction::findOrFail($transaction_id);
                    $paymentAmount = $transaction->item_qty * $transaction->item_unit_cost;

                    if ($paymentAmount > $customer->balance - $totalPayment) {
                        throw new \Exception("Insufficient balance for transaction ID: $transaction_id");
                    }

                    Payment::create([
                        'date' => now(),
                        'transaction_id' => $transaction->id,
                        'source' => 'wallet',
                        'plan_id' => null,
                        'amount' => $paymentAmount,
                        'auth_code' => null,
                        'auth_channel' => 'wallet',
                        'auth_description' => 'Wallet payment',
                        'auth_personnel' => auth()->user()->name ?? 'System',
                        'status' => 'paid',
                        'created_by' => auth()->id(),
                    ]);

                    $totalPayment += $paymentAmount;
                }

                // Deduct total payment from customer balance
                $customer->balance -= $totalPayment;
                $customer->updated_by = auth()->id();
                $customer->save();
            }
            
            DB::commit();

            return response()->json([
                'message' => 'Deposit and payments created successfully',
                'deposit' => $deposit,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to process payment: ' . $e->getMessage(),
            ], 500);
        }*/

        
    }

    // Update an existing payment
    public function update(Request $request, $id)
    {
        $request->validate([
            'date'          => 'sometimes|date',
            'bank_id'       => 'nullable|numeric|exists:finance_branch_banks,id',
            'customer_id'   => 'nullable|numeric|exists:crm_customers,id',
            'mode_id'       => 'required|numeric|exists:finance_payment_modes,id',
            'amount'        => 'required|numeric|min:1',
            'collected_by'  => 'sometimes|numeric',
            'collected_at'  => 'sometimes|date',
            'order_id'      => 'sometimes|numeric',
            'transactions'  => 'nullable|array',
            'transactions.*'=> 'sometimes|exists:finance_transactions,id',
            'trans_type'    => 'sometimes|string',
        ]);

        $payment = $this->finance_payment_update($request, $id);

        return response()->json([
            'message' => 'Payment updated successfully',
            'payment' => $payment,
        ], is_string($payment) ? 500 : 200);
    }

    // Delete a payment
    
}

    /*
    public function store(Request $request)
    {
        $this->validate($request, [
            'transaction_id'         => 'required', 
        ]);

        $transaction = Transaction::where('id', '=', $request->input('transaction_id'))->first();
        $patient = Patient::where('id', '=', $transaction->patient_id)->first();

        //Check if the patient has enough money in Wallet
        if ($patient->balance >= $transaction->item_total){
            $this->createPayment($transaction->id, $transaction->item_total, NULL);
            $patient->balance = $patient->balance - $transaction->item_total;
            $patient->save();
            $message = "Transaction has been paid for.";
            $icon = "success";
        }
        else{
            $message = "Insufficient Balance";
            $icon = "error";
        }

        return response()->json([
            'patient' => $patient,
            'transaction' => $transaction,
            'message' => $message,
            'icon' => $icon,
        ]);

    }*/