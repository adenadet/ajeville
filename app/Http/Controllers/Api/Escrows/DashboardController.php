<?php

namespace App\Http\Controllers\Api\Escrows;

use App\Http\Controllers\Controller;
use App\Http\Traits\Escrows\DisbursementTrait;
use App\Http\Traits\Escrows\DisputeTrait;
use App\Http\Traits\Escrows\PaymentTrait;
use App\Http\Traits\Escrows\ProductTrait;
use App\Http\Traits\Escrows\TransactionTrait;
use App\Http\Traits\Ums\UserTrait;
use App\Models\Escrows\Transaction;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use DisbursementTrait, DisputeTrait, PaymentTrait, ProductTrait, TransactionTrait, UserTrait;
    public function index()
    {
        if ($_GET['type'] == 'admin'){
            $first_day_of_week = new DateTime('this week');

            return response()->json([
                'disbursements' => $this->escrow_disbursement_get_all('all', null, true, true, 1),
                'disputes' => $this->escrow_dispute_get_all('active', null, true, true, 1),
                'payments' => $this->escrow_payments_get_all('all', null, true, true, 1),
                'products' => $this->escrow_products_all('my', null, true, true, 1), 
                'transactions' => $this->escrow_transaction_get_all('admin', ['end_date' => date('Y-m-d'), 'start_date' => $first_day_of_week->format('Y-m-d')], true, true, 1), 
                'users' => $this->ums_user_get_all('all', null, true, true),
            ]);
        }
        else{
            $payments = Transaction::selectRaw('DATE(created_at) as date, SUM(amount) as total_paid')
                    ->where('status', '>=', Transaction::StatusPaidAwaitingDelivery) // or include other relevant "paid" statuses
                    ->where('created_at', '>=', Carbon::now()->subDays(30))
                    ->where('seller_id', '=', auth('api')->id() ?? Auth::id())
                    ->groupBy(DB::raw('DATE(created_at)'))
                    ->orderBy('date', 'desc')
                    ->get();
                    
            return response()->json([
                'payments' => $payments,
                'products' => $this->escrow_products_all('my', null, true, true, 1), 
                'transactions' => $this->escrow_transaction_get_all('mine', null, true, true, 1), 
            ]);
        }
        
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
