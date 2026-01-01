<?php

namespace App\Http\Controllers\Api\Loans;

use App\Http\Controllers\Controller;
use App\Models\Loans\Account;
use App\Models\Loans\CheckListItem;
use Illuminate\Http\Request;

class CheckListController extends Controller
{
    public function index()
    {
        
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'loan_id' => 'required|numeric',
            'requirements'=> 'required|array',
        ]);

        foreach ($request->input('requirements') as $requirement){
            CheckListItem::create([
                'loan_id' => $request->input('loan_id'),
                'requirement_id' => $requirement['requirement_id'],
                'status' => $requirement['status'],
                'comment' => $requirement['comment'],
                'loan_officer_id' => auth('api')->id(),
            ]);
        }

        return response()->json([      
            'account' => Account::where('id','=', $request->input('loan_id'))->with(['user', 'type',])->first(),
            'checklist' => CheckListItem::where('loan_id', '=', $request->input('loan_id'))->with(['account_officer', 'requirement'])->get(),
        ]);

    }

    public function show($id)
    {
        return response()->json([      
            'account' => Account::where('id','=', $id)->with(['user', 'type',])->first(),
            'checklists' => CheckListItem::where('loan_id', '=', $id)->with(['account_officer', 'requirement'])->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $checklist = CheckListItem::where('id', '=', $id)->first();


        $checklist->status = $request->input('status');
        $checklist->comment = $request->input('comment');
        $checklist->loan_officer_id = auth('api')->id();
        
        $checklist->save();

        return response()->json([      
            'account' => Account::where('id','=', $request->input('loan_id'))->with(['user', 'type',])->first(),
            'checklist' => CheckListItem::where('loan_id', '=', $request->input('loan_id'))->with(['account_officer', 'requirement'])->get(),
        ]);
    }

    public function destroy($id)
    {
        //
    }
}
