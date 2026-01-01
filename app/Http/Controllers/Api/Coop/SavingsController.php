<?php

namespace App\Http\Controllers\Api\Coop;

use App\Http\Controllers\Controller;
use App\Http\Traits\Coop\SavingTrait;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Branch;
use App\Models\Bank;
use App\Models\Payment;
use App\Models\Saving;
use App\Models\SavingType;

class SavingsController extends Controller
{
    use SavingTrait;
    public function branch($id)
    {
        $saving_types = $this->coop_saving_account_type_get_all('active', null, false, false, null);
        $branches = Branch::select('id', 'name')->where('id', '=', $id)->with('users')->get();
        return response()->json([
            'branches' => $branches,       
            'saving_types' => $saving_types,       
        ]);
    }

    public function initials()
    {
        $savings = $this->coop_saving_account_get_all('active', null, true, false, null);
        $saving_types = $this->coop_saving_account_type_get_all('active', null, false, false, null);
        $banks = $this->coop_settings_bank_accounts();
        $branches = Branch::all();
        $accounts = Bank::all();
        return response()->json([
            'accounts' => $accounts,
            'banks' => $banks,       
            'branches' => $branches,       
            'saving_types' => $saving_types,       
            'savings' => $savings,       
            ]);
    }

    public function index()
    {
        $branches = Branch::select('id', 'name')->with('users')->get();
        return response()->json([
            'branches' => $branches,       
            'saving_types' => $this->coop_saving_account_type_get_all('active', null, false, false, null),       
        ]);
    }

    public function balance($id)
    {
        $saving = $this->coop_saving_account_get_by('uuid', $id, $_GET['viewer'] ?? 'mine', true);
        $balance = $saving ?  1.9 * ($saving->balance - $saving->fixed) : 0;
        return response()->json(['balance' => $balance]);
    }

    public function store(Request $request)
    {
        if ($request->input('user_id')){
            $this->validate($request, [
                'user_id' => 'required|numeric',
                'branch_id' => 'required|numeric',
            ]);

            $branch_id = $request->input('branch_id'); 
            $user_id = $request->input('user_id'); 
            }
        else{
            $branch_id = $request->input('branch_id'); 
            $user_id = auth('api')->id(); 
            }

        $this->validate($request, [
            'saving_type_id' => 'required|numeric',
            'name' => 'required|string',
            'target'=> 'required|numeric',
        ]);

        $saving = $this->coop_saving_account_create($request);

        return response()->json([
            'saving' => $saving,       
            'status' => 'Successfully created',
        ], is_string($saving) ? 500 : 200);
    }

    public function show($id)
    {
        return response()->json([
            'saving' => $this->coop_saving_account_get_by('uuid', $id, $_GET['viewer'] ?? 'mine', true),
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'saving_type_id' => 'required|numeric',
            'name' => 'required|string',
            'target'=> 'required|numeric',
        ]);

        $saving = $this->coop_saving_account_update($request, $id);

        return response()->json([
            'saving' => $saving,       
            'message' => is_string($saving) ? $saving : 'Successfully updated',
        ], is_string($saving) ? 500 : 200);
    }

    public function destroy($id)
    {

        $saving = $this->coop_saving_account_deactivate($id);

        return response()->json([
            'saving' => $saving,       
            'message' => is_string($saving) ? $saving : 'Successfully deactivated',
        ], is_string($saving) ? 500 : 200);
    }
}
