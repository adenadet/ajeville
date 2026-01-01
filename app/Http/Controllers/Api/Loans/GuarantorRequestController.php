<?php

namespace App\Http\Controllers\Api\Loans;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use App\Http\Controllers\Controller;
use App\Http\Traits\GuarantorTrait;

use App\Models\Loans\Account;
use App\Models\Loans\Guarantor;
use App\Models\Loans\GuarantorRequest;

use App\Mail\Guarantor\ConfirmMail;
use App\Mail\Guarantor\GuaranteedMail;
use App\Mail\Guarantor\RequestMail;
use App\Mail\Guarantor\ThanksMail;
use App\Models\Country;
use App\Models\Loans\Type;



class GuarantorRequestController extends Controller
{
    use GuarantorTrait;
    public function destroy($id)
    {
        $loan_id =$this->guarantor_delete_request($id);

        return response()->json([
            'status' => 'Success',
            'loan_id' => $loan_id,
            //'guarantors' => GuarantorRequest::where('loan_id', '=', $id)->get(),
            //'nations' => Country::select('id', 'name')->orderBy('name', 'ASC')->get(),
        ]);
    }
    public function index()
    {
        
    }
    public function show($id)
    {
        $request = GuarantorRequest::where('id', '=', $id)->first();

        $account = Account::where('id', '=',  $request->loan_id)->with(['guarantor_requests', 'user', 'type'])->first();
        if ($request->status == 1){
            return response()->json([
                'status' => 'upload',
                'message' => 'Kindly upload the files',
                'account' => $account,
                'guarantor' => Guarantor::where('request_id', '=', $id)->first(),
                'guarantor_request' => GuarantorRequest::where('id', '=', $id)->first(),
                'nations' => Country::select('id', 'name')->orderBy('name', 'ASC')->get(),
            ]);    
        }
        else if ($request->status == 2){
            return response()->json([
                'status' => 'Error',
                'message' => 'You have already rejected this loan',
                'account' => $account,
            ]);
        }
        else{
            return response()->json([
                'status' => 'Pending',
                'account' => $account,
                'guarantor' => Guarantor::where('request_id', '=', $id)->first(),
                'guarantor_request' => GuarantorRequest::where('id', '=', $id)->first(),
                'nations' => Country::select('id', 'name')->orderBy('name', 'ASC')->get(),
            ]);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'request_id'=> 'required|numeric',
            'title'=> 'required',
            'first_name'=> 'required',
            'middle_name'=> 'sometimes',
            'last_name'=> 'required',
            'relationship'=> 'required',
            'email'=> 'required|email',
            'phone'=> 'required',
            'employer'=> 'required',
            'employer_address'=> 'required',
            'employer_phone'=> 'required',
            'employer_email'=> 'required|email',
            'marital_status'=> 'required',
            'residential_address'=> 'required',
            'dob' => 'required|date',
            'net_income'=> 'required',
        ]);

        $guarantor = $this->guarantor_confirm_request($request);
        $guarantor_request = GuarantorRequest::where('id', '=', $guarantor->request_id)->first();

        return response()->json([
            'status' => 'Pending',
            'account' => Account::where('id', '=',  $guarantor_request->loan_id)->with(['user', 'type'])->first(),
            'guarantor' => $guarantor,
            'guarantor_request' => $guarantor_request,
            'nations' => Country::select('id', 'name')->orderBy('name', 'ASC')->get(),
        ]);
    }

    
    public function update(Request $request, $id)
    {
        $guarantor = $this->guarantor_complete_request($request, $id);
        
        $guarantor_request = GuarantorRequest::where('id', $id)->first();

        return response()->json([
            'status' => 'Pending',
            'account' => Account::where('id', '=',  $guarantor_request->loan_id)->with(['user', 'type'])->first(),
            'guarantor' => $guarantor,
            'guarantor_request' => $guarantor_request,
            'nations' => Country::select('id', 'name')->orderBy('name', 'ASC')->get(),
        ]);
    }   
}