<?php

namespace App\Http\Controllers\Api\EMR\Insurance;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\InsuranceTrait;
use App\Http\Traits\EMR\VisitTrait;
use App\Models\Finance\Payment;
use App\Models\Finance\Transaction;
use App\Models\Insurance\Plan;
use App\Models\Insurance\Provider;
use Illuminate\Http\Request;
 
class DashboardController extends Controller
{
    use InsuranceTrait, VisitTrait;
    public function index()
    {
        $providers = Provider::where('status', '=', 0)->pluck('id');
        return response()->json([
            'active_visits' => $this->visit_get_all('active', request()->cookie('current_branch'), null, false, false, $_GET['page'] ?? 1),
            'suspended_providers' => $providers->count(),
            'suspended_plans' => Plan::where('status', '=', 0)->orWhereIn('provider_id', $providers)->count(),
            'unconfirmed_transactions' => Payment::where('source', '=', 2)->where('status', '=', 0)->with(['plan.provider', 'transaction.items'])->paginate(40),
            'uncovered_transactions' => $this->insurance_transaction_get_all('uncovered', null, true, true, $_GET['page'] ?? 1),
        ]);
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
