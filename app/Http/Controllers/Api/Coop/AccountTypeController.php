<?php

namespace App\Http\Controllers\Api\Coop;

use App\Http\Controllers\Controller;
use App\Http\Traits\Coop\SavingTrait;
use Illuminate\Http\Request;

use App\Models\Coop\AccountType;

class AccountTypeController extends Controller
{
    use SavingTrait;
    public function index()
    {
        return response()->json([
            'account_types' => $this->coop_saving_account_type_get_all('active', null, false, false, null),       
        ]);
    }

    public function store(Request $request)
    {
        $account_type = $this->coop_saving_account_type_create($request);

        return response()->json([
            'account_type' => $account_type,
            'message' => is_string($account_type) ? $account_type : 'Account Type Created Successfully',
        ], is_string($account_type) ? 400 : 200);
    }

    public function show($id)
    {
        return response()->json([
            'account_type' => $this->coop_saving_account_type_get_by('uuid', $id, $_GET['viewer'] ?? 'admin', true),
        ]);
    }

    public function update(Request $request, $id)
    {
        $account_type = $this->coop_saving_account_type_update($request, $id);

        return response()->json([
            'account_type' => $account_type,
            'message' => is_string($account_type) ? $account_type : 'Account Type Created Successfully',
        ], is_string($account_type) ? 400 : 200);
    }

    public function destroy($id)
    {
        $account_type = $this->coop_saving_account_type_deactivate($id);

        return response()->json([
            'account_type' => $account_type,
            'message' => is_string($account_type) ? $account_type : 'Account Type Deactivated Successfully',
        ], is_string($account_type) ? 500 : 200);
    }

}
