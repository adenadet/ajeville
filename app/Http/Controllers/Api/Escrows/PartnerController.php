<?php

namespace App\Http\Controllers\Api\Escrows;

use App\Http\Controllers\Controller;
use App\Http\Traits\Escrows\ProductTrait;
use App\Http\Traits\Escrows\TransactionTrait;
use App\Http\Traits\Ums\UserTrait;
use App\Models\Escrows\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    use ProductTrait, TransactionTrait, UserTrait;
    public function index()
    {
        return response()->json([
            'partners' => $this->escrow_partners_get_all('mine', null, false, true, $_GET['page'] ?? 1), 
        ]);
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //type=unique_id&detailed=0
        if ((isset($_GET['type'])) && (isset($_GET['detailed']))){
            return response()->json([
                'partner' => $this->ums_user_get_by_id($_GET['type'], $id, $_GET['detailed']),
            ]);    
        }
        else{
            return response()->json([
                'partner' => $this->ums_user_get_by_id('id', $id, false),
                'products' => $this->escrow_products_all('owner', $id, true, false, null),
                'reviews' => [],
                'transaction_count' => $this->escrow_partners_get_count('transactions', $id),
            ]);
        }
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
