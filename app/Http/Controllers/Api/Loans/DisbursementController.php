<?php

namespace App\Http\Controllers\Api\Loans;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use App\Http\Traits\LoanAccountTrait;

use App\Models\Loans\Account;
use App\Models\Loans\Disbursement;

use App\Mail\Loans\DisbursementMail;


class DisbursementController extends Controller
{
    use LoanAccountTrait;
    public function index()
    {
        return response()->json([      
            'accounts' => $this->account_all('disbursement', $_GET['page'] ?? 1),
        ]);
    }

    public function store(Request $request)
    {
        Disbursement::create([
            'loan_id' => $request->input('loan_id'),
            'amount' => $request->input('amount'),
            'payment_date' => $request->input('payment_date'),
            'description' => $request->input('description'),
            'created_by' => auth('api')->id(),
            'updated_by' => auth('api')->id(),
        ]);

        $loan = Account::where('id', '=', $request->input('loan_id'))->with(['account_officer', 'type', 'user'])->first(); $loan->status = 16; $loan->save();

        Mail::to($loan->user->email)->send(new DisbursementMail($loan));
        
        return response()->json([      
            'accounts' => Account::where('status', 15)->with(['account_officer', 'user', 'type'])->paginate(20),
        ]);
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $disbursement = Disbursement::find($id);

        $disbursement->loan_id = $request->input('loan_id');
        $disbursement->amount = $request->input('amount');
        $disbursement->payment_date = $request->input('payment_date');
        $disbursement->description = $request->input('description');
        $disbursement->updated_by = date('Y-m-d H:i:s');
        
        $disbursement->save();
        
        return response()->json([      
            'accounts' => Account::where('status', 15)->with(['account_officer', 'user', 'type'])->paginate(20),
        ]);
    }

    public function destroy($id)
    {
        //
    }
}
