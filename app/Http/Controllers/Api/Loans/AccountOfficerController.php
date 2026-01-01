<?php

namespace App\Http\Controllers\Api\Loans;

use App\Http\Controllers\Controller;
use App\Models\Loans\Account;
use App\Models\Loans\AccountOfficer;
use Illuminate\Http\Request;

use App\Http\Traits\LoanAccountTrait;
use App\Http\Traits\LogTrait;

class AccountOfficerController extends Controller
{
    use LoanAccountTrait, LogTrait;

    public function destroy($id)
    {
        //
    }
    
    public function index()
    {
        return response()->json([
            'accounts' => $this->account_all('account_officer', $_GET['page']),
        ]);
    }

    public function risks()
    {
        return response()->json([
            'accounts' => $this->account_all('risk_officer', $_GET['page']),
        ]);
    }

    public function show($id)
    {
        //
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'account_id' => 'required|numeric',
            'staff_id' => 'required|numeric',
        ]);

        $accounts = AccountOfficer::where('account_id', '=', $request->input('account_id'))->get();

        foreach ($accounts as $account) {
            $account->status = 2;
            $account->deleted_by = auth('api')->id();
            $account->deleted_at = date('Y-m-d H:i:s');
            $account->save();
        }

        AccountOfficer::create([
            'account_id' => $request->input('account_id'),
            'staff_id' => $request->input('staff_id'),
            'status' => $request->input('status') ?? 1,
            'created_by' => auth('api')->id(),
            'updated_by' => auth('api')->id(),
        ]);

        return response()->json([
            'accounts' => Account::where('user_id', auth('api')->id())->with(['repayments', 'user', 'type'])->paginate(20),
            'account' => Account::where('id', '=',  $request->input('account_id'))->first(),
        ]);
    }

    
    public function update(Request $request, $id)
    {
        //
    }    
}
