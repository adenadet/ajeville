<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Traits\Finance\AccountTrait;
use App\Http\Traits\Finance\SettingTrait;
use App\Http\Traits\Operations\BranchTrait;
use Illuminate\Http\Request;

class BranchAccountController extends Controller
{
    use AccountTrait, BranchTrait, SettingTrait;

    public function destroy(string $id)
    {
        $branch_account = $this->finance_setting_branch_account_deactivate($id);

        return response()->json([
            'message' => 'Branch account deactivated/reactivated successfully',
            'branch_account' => $branch_account,
        ], is_string($branch_account) ? 500 : 200);
    }

    public function index()
    {
        //echo request()->cookie('current_branch');
        return response()->json([
            'branch_accounts' => $this->finance_setting_branch_account_get_all($_GET['type'] ?? 'branch', $_GET['branch_id'] ?? null, true, true, $_GET['page'] ?? 1),
        ]);
    }

    public function initials()
    {
        return response()->json([
            'banks' => $this->finance_setting_all_banks_get_all('active', null, false, false, null),
            'branches' => $this->operation_branch_get_all(false, false, null),
        ]);
    }

    public function show(string $id)
    {
        $branch_account = $this->finance_account_get_by(null, $id, true);

        return response()->json([
            'branch_account' => $branch_account,
        ], is_string($branch_account) ? 404 : 200);
    }
    
    public function store(Request $request)
    {
        $data = $request->validate([
            'bank_id' => 'required|integer',
            'branch_id' => 'nullable|integer',
            'account_number' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'status' => 'nullable|boolean',
        ]);

        $branch_account = $this->finance_setting_branch_account_create($data);

        return response()->json([
            'message' => 'Branch account created successfully',
            'branch_account' => $branch_account,
        ], is_string($branch_account) ? 500 : 201);
    }

    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'bank_id' => 'required|integer',
            'branch_id' => 'nullable|integer',
            'account_number' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'status' => 'nullable|boolean',
        ]);

        $branch_account = $this->finance_setting_branch_account_update($data ,$id);

        return response()->json([
            'message' => 'Branch account created successfully',
            'branch_account' => $branch_account,
        ], is_string($branch_account) ? 500 : 201);
    }
}
