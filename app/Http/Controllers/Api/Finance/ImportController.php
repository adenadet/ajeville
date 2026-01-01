<?php
namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    /**
     * Simple placeholder for future import endpoints (invoices, transactions)
     */
    public function importInvoices(Request $req)
    {
        // TODO: implement bulk invoice import if needed (CSV/Excel)
        return response()->json(['message' => 'Not implemented'], 501);
    }
}
