<?php
namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Traits\Finance\ExpenseTrait;
use App\Http\Traits\Finance\MainTransactionTrait;
use App\Http\Traits\Operations\BranchTrait;
use App\Http\Traits\Procurement\VendorTrait;
use App\Models\Finance\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    use BranchTrait, ExpenseTrait, VendorTrait;

    public function approve(Request $request, $id){
        $invoice = $this->finance_invoice_confirm($request, $id);

        return response()->json([
            'invoice' => $invoice,
        ], is_string($invoice) ? 500 : 200);
    }

    public function expense($id){
        $expense = $this->finance_expense_create_from('invoice', $id);

        return response()->json([
            'expense' => $expense,
        ], is_string($expense) ? 500 : 200);
    }

    public function index(){
        return response()->json([
            'invoices' => $this->finance_invoice_get_all($_GET['status'] ?? 'all', $_GET, true, true),
        ], 201);
    }

    public function initials(){
        return response()->json([
            'branches' => $this->operation_branch_get_all( false, false, null),
            'expense_types' => $this->finance_expense_type_get_all('active', null, false, false, null),
            'vendors' => $this->procurement_vendor_get_all('active', [], false, false, null),
        ], 201);
    }

    public function store(Request $request)
    {
        /*
        $data = $req->validate([
            'related_type' => 'nullable|string',
            'related_id' => 'nullable|integer',
            'customer_id' => 'nullable|integer|exists:customers,id',
            'lines' => 'required|array',
            'issue_date' => 'nullable|date',
            'due_date' => 'nullable|date'
        ]);

        $sub = 0;
        foreach ($data['lines'] as $l) {
            $sub += ($l['quantity'] ?? 1) * ($l['unit_price'] ?? 0);
        }
        $tax = $req->input('tax', 0);
        $total = $sub + $tax;

        $invoice = Invoice::create([
            'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6)),
            'related_type' => $data['related_type'] ?? null,
            'related_id' => $data['related_id'] ?? null,
            'customer_id' => $data['customer_id'] ?? null,
            'sub_total' => $sub,
            'tax' => $tax,
            'total' => $total,
            'issue_date' => $data['issue_date'] ?? now()->toDateString(),
            'due_date' => $data['due_date'] ?? null,
            'status' => 'issued',
            'lines' => $data['lines']
        ]);
        */

        $invoice = $this->finance_invoice_create($request);

        return response()->json(['invoice' => $invoice], is_string($invoice) ? 500 :201);
    }

    public function show($id)
    {
        $inv = Invoice::with('transactions','customer')->findOrFail($id);
        return response()->json($inv);
    }

    /**
     * Mark invoice as paid (creates a payment transaction)
     */
    public function markPaid(Request $req, $id)
    {
        $inv = Invoice::findOrFail($id);
        $data = $req->validate([
            'amount' => 'required|numeric|min:0.01',
            'bank_account_id' => 'nullable|integer|exists:bank_accounts,id',
            'reference' => 'nullable|string',
            'transaction_date' => 'nullable|date'
        ]);

        $amount = $data['amount'];
        $tx = $this->createTransaction('payment', $amount, [
            'reference' => $data['reference'] ?? null,
            'bank_account_id' => $data['bank_account_id'] ?? null,
            'related_type' => Invoice::class,
            'related_id' => $inv->id,
            'transaction_date' => $data['transaction_date'] ?? now()->toDateString(),
            'meta' => ['note' => 'Invoice payment via API']
        ]);

        // update invoice status
        if ($inv->amount_paid + $amount >= $inv->total) {
            $inv->status = 'paid';
        } else {
            $inv->status = 'partially_paid';
        }
        $inv->save();

        return response()->json(['transaction' => $tx, 'invoice' => $inv]);
    }

    public function update(Request $request, $id)
    {
        $invoice = $this->finance_invoice_update($request, $id);

        return response()->json(['invoice' => $invoice], is_string($invoice) ? 500 :200);
    }
}
