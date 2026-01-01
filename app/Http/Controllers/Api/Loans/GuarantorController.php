<?php

namespace App\Http\Controllers\Api\Loans;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use App\Http\Controllers\Controller;
use App\Http\Traits\FileTrait;
use App\Http\Traits\GuarantorTrait;
use App\Http\Traits\LogTrait;

use App\Models\Loans\Account;
use App\Models\Loans\Guarantor;
use App\Models\Loans\GuarantorRequest;

use App\Mail\Guarantor\RequestMail;
use App\Models\Loans\Type;

class GuarantorController extends Controller
{
    use GuarantorTrait, LogTrait;

    public function add(Request $request){
        $this->guarantor_new_request($request);
        
        return response()->json([
            'message' => 'Guarantor added successfully',       
        ]);  
    }
    
    public function destroy($id)
    {
        $loan_id = $this->guarantor_delete_request($id);

        return response()->json([
            'account' => Account::where('id', '=', $loan_id)->with('guarantor_requests.guarantor')->first(),
            'message' => 'Guarantor added successfully',
            'guarantors' => $this->guarantor_get_requests($id),
        ]);
    }
    
    public function display($id)
    {
        return response()->json([
            'account' => Account::where('id', '=', $id)->with('guarantor_requests.guarantor')->first(),
            'guarantors' => $this->guarantor_get_requests($id),
        ]);
    }

    public function index()
    {
        $loans = Account::where([['status', '<', 10],['user_id', '=', auth('api')->id()]])->pluck('id');

        return response()->json([
            'requests' => GuarantorRequest::whereIN('loan_id',$loans)->with(['account', 'guarantor'])->latest()->paginate(10),
        ]);
    }

    public function initials($id)
    {
        $request = GuarantorRequest::where('id', '=', $id)->first();

        if ($request->status == 1){
            return response()->json([
                'status' => 'Error',
                'message' => 'You have already guaranteed this loan',
                'account' => Account::where('id', '=', $request->loan_id)->first(),
            ]);    
        }
        else if ($request->status == 2){
            return response()->json([
                'status' => 'Error',
                'message' => 'You have already rejected this loan',
                'account' => Account::where('id', '=',  $request->loan_id)->with('guarantor_requests')->first(),
            ]);
        }
        else{
            return response()->json([
                'account' => Account::where('id', '=', $id)->with('guarantor_requests')->first(),
                'guarantors' => GuarantorRequest::where('loan_id', '=', $id)->get(),
            ]);
        }
    }

    public function resend($id)
    {
        $gr = GuarantorRequest::where('id', '=', $id)->first();
        $loan = Account::where('id', '=', $gr->loan_id)->with('user')->first();
        //echo $gr->email;
        $error = Mail::to($gr->email)->send(new RequestMail($loan, $gr));
        if ($error){}
        return response()->json([
            'message' => 'Guarantor added successfully',       
        ]);  
    }

    public function reset(Request $request, $id)
    {
        GuarantorRequest::where('loan_id', '=', $id)->delete();
        $loan = Account::where('id', '=',  $id)->first();
        foreach ($request->input('guarantors') as $guarantor){
            $this->guarantor_new_request($loan, $guarantor);
        }
    }

    public function show($id)
    {
        return response()->json([
            'guarantor' => Guarantor::where('id', '=', $id)->with(['loan', 'residential_address_verification', 'employer_address_verification'])->first(),
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'loan_id' => 'required|numeric',
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
            'address'=> 'required',
            'dob' => 'required|date',
            'net_income'=> 'required',
        ]);

        $this->guarantor_confirm_request($request);

        return response()->json([
            'message' => 'The loan has been successfully guaranteed',
        ]);
    }

    public function update(Request $request, $id)
    {
        
    }
}
