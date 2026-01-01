<?php

namespace App\Http\Controllers\Api\Loans;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Branch;
use App\Models\Finance\AllBank;
use App\Models\Finance\Bank;
use App\Models\Finance\Bureau;
use App\Models\Finance\BureauProduct;
use App\Models\Loans\Account;
use App\Models\Loans\Guarantor;
use App\Models\Loans\Type;
use App\Models\Loans\Repayment;
use App\Models\User;
use App\Models\Area;
use App\Models\Loans\CreditScore;
use App\Models\Loans\CheckListItem;
use App\Models\Loans\Disbursement;
use App\Models\Loans\TypeRequirement;
use App\Models\State;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

use App\Http\Traits\GeminiTrait;
use App\Http\Traits\LoanAccountTrait;
use App\Http\Traits\LogTrait;

class AccountController extends Controller{

    use GeminiTrait, LoanAccountTrait, LogTrait;

    public function credit_scores($id)
    {
        
    }
    
    public function customer($id)
    {
        return response()->json([
            'all_banks' => AllBank::select('id', 'bank_name')->orderBy('bank_name', 'ASC')->get(),
            'loan_types' => Type::where('status', '1')->get(),       
            'accounts' => Account::where('user_id', '=', $id)->with(['repayments', 'guarantor_requests', 'type', 'guarantors'])->latest()->paginate(10),
        ]);
    }

    public function destroy($id)
    {
        $loan = Account::where('id', '=', $id)->first();

        $loan->status = 3;
        $loan->deleted_by = auth('api')->id();
        $loan->deleted_at = date('Y-m-d H:i:s');
        $loan->save();

        return response()->json([      
            'accounts' => Account::where('user_id', auth('api')->id())->with([ 'guarantor_requests', 'repayments', 'user', 'type', ])->latest()->paginate(20),
        ]);
    }

    public function initials()
    {
        return response()->json([
            'all_banks' =>  $this->get_gemini_banks(),
            'accounts' => Account::where('user_id', auth('api')->id())->with(['repayments', 'guarantor_requests', 'guarantors'])->get(),
            'user'=> User::where('id', '=', auth('api')->id())->with(['customer_address', 'customer_accounts', 'next_of_kin'])->first(),
            'current_loan' => Account::where('user_id', auth('api')->id())->where('status', '<', 5)->with(['guarantor_requests', 'guarantors', 'repayments'])->first(),
            'loan_types' => $this->get_gemini_loan_products(),
        ]);
    }

    public function index()
    {
        return response()->json([      
            'accounts' => $this->account_all('mine', $_GET['page']),
        ]);
    }

    public function pending()
    {
        return response()->json([      
            'accounts' => $this->account_all('pending', $_GET['page']),
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'account' => $this->account_details($id),
            'repayments' => Repayment::where('loan_id', '=', $id)->with(['bank'])->latest()->paginate(20),     
        ]);    
    }

    public function staffs()
    {
        return response()->json([
            'accounts' => Account::where('status', '<=', 13)->with(['bank', 'type', 'user', 'account_officer.staff'])->latest()->paginate(50),
        ]);
    }

    public function staff_show($id)
    {
        $account = Account::where('id', '=', $id)->with(['bank', 'type', 'user', 'account_officer'])->first();
        $requirements = TypeRequirement::select('requirement_id')->where('loan_type_id', '=', $account->type_id)->with('requirement')->get();
        $loan_requirements = CheckListItem::where('loan_id', '=', $id)->with('requirement')->get();

        return response()->json([
            'account' => $account,
            'requirements' => $requirements,
            'loan_requirements' => $loan_requirements,
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'loan_type_id' => 'required|numeric',
            'amount'=> 'required|numeric',
            'duration'=> 'required|numeric',
            'bank_id' => 'required|numeric',
            'acct_name' => 'required',
            'acct_number' => 'required|numeric',
            'frequency' => 'required',
        ]);

        $loan = $this->account_create_new($request);

        $this->log_createNewLoanActivity(auth('api')->user(), $loan);
        
        return response()->json([
            'current_loan' => $loan, 'message' => 'Successfully created, kindly add guarantors',
        ]);
    }
    
    public function update(Request $request, $id)
    {
        $loan = Account::find($id);
        $loan_type = Type::where('ProductCode', '=', $request['loan_type_id'])->with('requirements')->first();
            
        if ($loan_type->interest_type == 'Flat'){
            $principal = $request->input('amount');
            $rate = 0.6 ;
            $time = $request->input('frequency') == 'weeks' ? ($request->input('duration') / 52) : ($request->input('duration') / 12);
            $totalPayment = $request->input('amount') * (1 + ($rate * $time));
            $emiMonthly =  $totalPayment / $request->input('duration');
        }

        else if($loan_type->interest_type == 'Reducing'){    
            $principal = $request->input('amount');
            $interest = $request->input('frequency') == 'weeks' ? $loan_type->percentage / 5200 : $loan_type->percentage / 1200;
            $x = pow(1 + $interest, $request->input('duration'));
            $emiMonthly =  ($principal  * $x * $interest) / ($x-1);    
        }

        $bank = AllBank::where('bank_code', '=', $request->input('bank_id'))->first();

        $signaturePad = NULL;
        if (!is_null($request->input('signature'))){
            $signature_pad = time().".".explode('/',explode(':', substr( $request->input('signature'), 0, strpos($request->input('signature'), ';')))[1])[1];
            \Image::make($request->input('signature'))->save(public_path('img/consents/').$signature_pad);
            $signaturePad = $signature_pad;
        }

        $loan->type_id = $loan_type->id;
        $loan->user_id = $request->input('user_id') ?? auth('api')->id();
        $loan->amount = $request->input('amount');
        $loan->payable = round(($emiMonthly * $request->input('duration')), 2);
        $loan->emi = round(($emiMonthly), 2);
        $loan->duration = $request->input('duration');
        $loan->frequency = $request->input('frequency');
        $loan->name = $request->input('name') ?? 'Loan';
        $loan->bank_id = $bank->id;
        $loan->acct_name = $request->input('acct_name');
        $loan->acct_number = $request->input('acct_number');
        $loan->signature = $signaturePad;
        $loan->total_paid = 0;
        $loan->status_date = date('Y-m-d H:i:s');
        $loan->request_by = $request->input('user_id') ?? auth('api')->id();
        $loan->updated_by = auth('api')->id();
        
        $loan->save();
        
        $this->log_activity_update_loan(auth('api')->user(), $loan);
        
        return response()->json([
            'loan' => $loan,
            'status' => 'success',
            'message' => 'Modifications',
            'accounts' => Bank::all(),
            'all_banks' => AllBank::orderBy('bank_name', 'ASC')->get(),
            'branches' => Branch::with('users.savings')->get(),      
            'loans' => $this->account_all('mine', 1),
        ]);
    }

    private function sendSMS($number, $message = "Thanks for registering"){
        $client = new Client();
        $url = 'http://customer.smsprovider.com.ng/api/?username=adenadet01@gmail.com&password=yetunde01&message='.$message.'&sender=DLCIK&mobiles='.$number;
        $response = $client->request('GET', $url);
        $body = json_decode($response->getBody() , true);
        
        return $body['status'];
    }
}
