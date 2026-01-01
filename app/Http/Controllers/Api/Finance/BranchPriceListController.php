<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\InsuranceTrait;
use App\Http\Traits\Finance\SettingTrait;
use App\Http\Traits\Operations\BranchTrait;
use App\Http\Traits\Operations\PriceListTrait;
use Illuminate\Http\Request;

class BranchPriceListController extends Controller
{
use BranchTrait, InsuranceTrait, PriceListTrait, SettingTrait;

    public function destroy(string $id)
    {
        $branch_price_list = $this->finance_setting_price_list_deactivate($id);

        return response()->json([
            'message' => 'Branch account deactivated/reactivated successfully',
            'branch_price_list' => $branch_price_list,
        ], is_string($branch_price_list) ? 500 : 200);
    }

    public function index()
    {
        return response()->json([
            'branch_price_lists' => $this->operation_branch_price_list_get_all($_GET['type'] ?? 'branch', $_GET['branch_id'] ?? null, true, true, $_GET['page'] ?? 1),
        ]);
    }

    public function initials()
    {
        return response()->json([
            'branches' => $this->operation_branch_get_all(false, false, null),
            'plans' => $this->insurance_provider_plan_get_all('branch', null, false, false, null),
            'price_lists' => $this->operation_price_list_get_all('active', null, false, false, null),             
        ]);
    }

    public function show(string $id)
    {
        $branch_price_list = $this->finance_account_get_by(null, $id, true);

        return response()->json([
            'branch_price_list' => $branch_price_list,
        ], is_string($branch_price_list) ? 404 : 200);
    }
    
    public function store(Request $request)
    {
        $data = $request->validate([
            'branch_id' => 'required|integer',
            'plan_id' => 'nullable|integer',
            'price_list_id' => 'required|integer',
            'status' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        $branch_price_list = $this->finance_setting_branch_price_list_create($data);

        return response()->json([
            'message' => 'Branch price list created successfully',
            'branch_price_list' => $branch_price_list,
        ], is_string($branch_price_list) ? 500 : 201);
    }

    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'branch_id' => 'required|integer',
            'plan_id' => 'nullable|integer',
            'price_list_id' => 'required|integer',
            'status' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        $branch_price_list = $this->finance_setting_price_list_update($data ,$id);
        
        return response()->json([
            'message' => 'Branch Price List updated successfully',
            'branch_price_list' => $branch_price_list,
        ], is_string($branch_price_list) ? 500 : 201);
    }
}
