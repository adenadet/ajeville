<?php

namespace App\Http\Controllers\Api\Invoice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice\Invoice;
use App\Models\Invoice\Item;
use App\Models\Invoice\Product;
use App\Models\Ums\Customer;

class InvoiceController extends Controller
{
    public function index()
    {
        return response()->json([
            'invoices' => Invoice::with(['customer', 'items.product'])->orderBy('created_at', 'DESC')->paginate(10), 
        ]);
    }

    public function initials()
    {
        return response()->json([
            'customers' => Customer::orderBy('first_name', 'ASC')->get(),
            'products' => Product::orderBy('description', 'ASC')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $invoice = Invoice::create([
            'unique_id' => 'INV-'.date('Ym').sprintf('%010d', rand(1, 10000)),
            'customer_id' => $request->input('customer_id'),
            'date' => $request->input('date'),
            'due_date' => $request->input('due_date') ?? date('Y-m-d', strtotime($request->input('date'). ' + 45 days')),
            'reference' => 'REF-'.rand(10, 3000),
            'tandc' => $request->input('tandc'),
            'sub_total' =>  0.00,
            'discount' => $request->input('discount') ?? 0.00,
            'logistics' => $request->input('discount') ?? 0.00,
            'taxes' => $request->input('taxes') ?? 0.00,
            'status' => 0,
        ]);
        $sub_total = 0.00;
        foreach ($request->input('items') as $item){
            $item = Item::create([
                'invoice_id' => $invoice->id,
                'product_id' => $item['product_id'],
                'unit_price' => $item['unit_price'],
                'quantity' => $item['quantity'],
                'discount' => $item['discount'] ?? 0.00,
            ]);

            $sub_total += ($item['unit_price'] * $item['quantity']);
        }

        $invoice->sub_total = $sub_total;
        $invoice->save();

        return response()->json([
            'invoice' => $invoice,
            'invoices' => Invoice::orderBy('created_at', 'DESC')->paginate(10), 
        ]);
    }

    public function show(string $id)
    {
        return response()->json([
            'invoice' => Invoice::where('id', '=', $id)->with(['customer', 'items'])->first(), 
        ]);
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
