<?php

namespace App\Http\Controllers\Api\Loans;

use App\Http\Controllers\Controller;
use App\Models\Loans\Account;
use Illuminate\Http\Request;
use App\Models\Loans\CPM;
use App\Models\Settings\CPMModule;

class CPMController extends Controller
{
    public function index()
    {
        return response()->json([
            'cpm' => CPMModule::with('templates')->get(),
        ]);
    }

    public function initials()
    {
        return response()->json([
            'modules' => CPMModule::with('templates')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'loan_id' => 'required|numeric',
            'detail'=> 'required',
        ]);

        CPM::create([
            'loan_id' => $request->input('loan_id'),
            'detail' => $request->input('detail'),
            'ao_id' => auth('api')->id(),
            'ao_at' => date('Y-m-d H:i:s'),
            'created_by' => auth('api')->id(),
            'updated_by' => auth('api')->id(),
        ]);

        return response()->json([
            'cpm' => CPM::latest()->paginate(20),
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'account' => Account::where('id', '=', $id)->with(['account_officer', 'user'])->first(),
            'cpm' => CPM::where('loan_id', '=', $id)->first(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'loan_id' => 'required|numeric',
            'detail'=> 'required',
        ]);

        $cpm = CPM::where('id', '=', $id)->first();

        $cpm->loan_id = $request->input('loan_id');
        $cpm->detail = $request->input('detail');
        $cpm->ao_id = auth('api')->id();
        $cpm->ao_at = date('Y-m-d H:i:s');
        $cpm->updated_by = auth('api')->id();
        
        $cpm->save();
        
        return response()->json([
            'cpm' => CPM::latest()->paginate(20),
        ]);
    }

    public function destroy($id)
    {
        //
    }
}
