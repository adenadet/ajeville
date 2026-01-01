<?php

namespace App\Http\Controllers\Api\Loans;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Traits\FileTrait;
use App\Http\Traits\LoanAccountTrait;

use App\Models\Loans\File;

class FileController extends Controller
{
    use FileTrait, LoanAccountTrait;
    public function confirm(Request $request, $id)
    {
        $loan_file = $this->account_file_confirmation($request, $id);

        return response()->json([
            'message' => $request->input('status') == 1 ? 'Successfully Approved' : 'Successfully Rejected',
            'files' => $this->file_get_loan_files_by_id($loan_file->loan_id),
        ]);
         
    }

    public function destroy($id)
    {
        $loan_file = File::where('id', '=', $id)->first();

        $loan_file->deleted_by = auth('api')->id();
        $loan_file->updated_by = auth('api')->id(); 
        $loan_file->deleted_at = date('Y-m-d H:i:s');

        $loan_file->save();

        return response()->json([
            'message' => 'Successfully deleted',
        ]);
         
    }

    public function index()
    {
        //
    }

    public function show($id)
    {
        return response()->json([
            'files' => $this->file_get_loan_files_by_id($id),
        ]);     
    }

    public function store(Request $request)
    {
        $file_name = $this->file_upload_by_type($request->file, 'pdf', 'uploads/files', $request->loan_id);

        $loan_file = File::create([
            'description' => $request->description,
            'loan_id' => $request->loan_id,
            'file_name' => $request->file_name,
            'source' => $file_name,
            'file_type' => $request->file_type,
            'status' => 0,
            'created_by' => auth('api')->id(),
            'updated_by' => auth('api')->id(),
        ]);

        return response()->json([
            'files' => $this->file_get_loan_files_by_id($loan_file->loan_id),
            'message' => 'Successfully uploaded',
        ]);
    }

    
    public function update(Request $request, $id)
    {
        $loan_file = File::where('id', '=', $id)->first();

        $loan_file->status = $request->input('status');
        $loan_file->approved_by = auth('api')->id();
        $loan_file->approved_date = date('Y-m-d H:i:s');

        $loan_file->save();

        return response()->json([
            'message' => 'Successfully updated',
        ]);
    }
}
