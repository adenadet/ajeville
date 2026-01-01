<?php

namespace App\Http\Controllers\Api\Loans;

use App\Http\Controllers\Controller;
use App\Http\Traits\LoanAccountTrait;
use App\Mail\Loans\AOConfirmMail;
use App\Mail\Loans\ERMConfirmMail;
use App\Mail\Loans\MDConfirmationMail;
use App\Models\Loans\Account;
use App\Models\Loans\ConfirmationAction;
use App\Models\Loans\ConfirmationMatrix;
use App\Models\Loans\ConfirmationMatrixItem;
use App\Models\Loans\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ConfirmationController extends Controller
{
    use LoanAccountTrait;
    public function index()
    {
        $roles = DB::table('model_has_roles')->where('model_id','=', 1)->pluck('role_id');
        $matrix = ConfirmationMatrix::pluck('id');
        
        $matrices = [];

        for ($i = 0; $i < count($matrix); $i++){
            
            $matrix_levels = [
                'matrix_id' => $matrix[$i],
                'status' => ConfirmationMatrixItem::whereIn('role_id', $roles)->pluck('stage_number'),
            ];

            array_push($matrices, $matrix_levels);
        }

        $loans = Account::where('loan_accounts.status', '>=', 6);
        for ($j = 0; $j < count($matrices); $j++){
            $loan_types = Type::where('matrix_id', '=', $matrices[$j]['matrix_id'])->pluck('id'); 
            $loans_by_type = Account::whereIn('type_id', $loan_types)->whereIn('status', $matrices[$j]['status']);
            $loans = $loans->union($loans_by_type);
        }

        return response()->json([
            'accounts' => $loans->with(['repayments', 'user', 'type'])->latest()->paginate(20),
        ]);
    }

    public function initials($id)
    {
        $loan = Account::where('id', '=', $id)->first();
        $loan_type = Type::where('id', '=', $loan->type_id)->first();
        $acts = $loan->status + 1;
        
        $actions = ConfirmationMatrixItem::where('stage_number', '<=', $acts)->where('stage_number', '!=', $loan->status)->where('matrix_id', '=', $loan_type->matrix_id)->with(['role'])->orderBy('stage_number', 'DESC')->get();
        $current = ConfirmationMatrixItem::where('stage_number', '=', $loan->status)->where('matrix_id', '=', $loan_type->matrix_id)->with(['role'])->first();

        return response()->json([
            'account' => $loan,
            'actions' => $actions,
            'current' => $current,
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'loan_id' => 'required|numeric', 
            'action' => 'required|numeric',
            'description' => 'sometimes', 
        ]);

        $loan = $this->account_details($request->input('loan_id'));
        
        $start_point = ConfirmationMatrixItem::select('role_id')->where('stage_number', '=', $loan->status)->where('matrix_id', '=', $loan->type->matrix->id)->with('role')->first();
        $end_point = ConfirmationMatrixItem::select('role_id')->where('stage_number', '=', $request->input('action'))->where('matrix_id', '=', $loan->type->matrix->id)->with('role')->first();

        if ($request->input('action') == 14){
            $action = ConfirmationAction::create([
                'loan_id' => $request->input('loan_id'),
                'action'  => $request->input('action'),
                'summary' => 'confirmed the Loan for disbursement',
                'description' => $request->input('description'),
                'start_stage' => $loan->status,
                'end_stage'=> 16,
                'created_by' => auth('api')->id(),
                'updated_by' => auth('api')->id(),
            ]);

            //Send a mail to confirmation to 1. ERM and 2. Marketing Team  
            Mail::to('s.akande@glazecredit.com')->send(new ERMConfirmMail($loan));
            Mail::to('marketing@glazecredit.com')->send(new AOConfirmMail($loan));
        }
        else{
            $action = ConfirmationAction::create([
                'loan_id' => $request->input('loan_id'),
                'action'  => $request->input('action'),
                'summary' => 'from '.$start_point->role->name.' to '.$end_point->role->name,
                'description' => $request->input('description'),
                'start_stage' => $loan->status,
                'end_stage'=> $request->input('action'),
                'created_by' => auth('api')->id(),
                'updated_by' => auth('api')->id(),
            ]);

            if ($request->input('action') == 13){
                Mail::to('o.ambode@glazecredit.com')->send(new MDConfirmationMail($loan));
            }
        }
        
        $loan->status = $request->input('action');
        $loan->updated_by = auth('api')->id();
        $loan->updated_at = date('Y-m-d H:i:s');

        $loan->save();

        return response()->json([
            'account' => $loan,
            'actions' => ConfirmationAction::where('loan_id', '=', $request->input('loan_id'))->orderBy('created_at', 'DESC')->with([])->paginate(20),
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'account' => Account::where('id', '=', $id)->with('type.matrix')->first(),
            'actions' => ConfirmationAction::where('loan_id', '=', $id)->with(['creator'])->latest()->paginate(20),
        ]);
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
