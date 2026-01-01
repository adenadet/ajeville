<?php

namespace App\Http\Controllers\Api\Loans;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Loans\Account;
use App\Models\Loans\Guarantor;
use App\Models\Loans\OtherFile;

class OtherController extends Controller
{
    public function display($id)
    {
        return response()->json([
            'account' => Account::where('id', '=', $id)->with('guarantor_requests.guarantor')->first(),
            'others' => OtherFile::where('account_id', '=', $id)->with('creator')->get(),
        ]);
    }
    public function index()
    {
        //
    }

    public function store(Request $request)
    {

        $folder = 'uploads/loan_files/';
        $file_name = ''; 
    
        if ($request->input('file_type') == "Image"){
            $name = $request->input('user_id')."-".$request->input('file_name').time().".".explode('/',explode(':', substr( $request->input('file'), 0, strpos($request->input('file'), ';')))[1])[1];
            \Image::make($request->input('file'))->save(public_path($folder).$name);
            $file_name = $folder.$name;
        }
        else if($request->input('file_type') == "PDF"){
            if (strpos($request->input('file'), ',') !== false) {
                //@list($encode, $request->input('file')) = explode(',', $request->input('file'));
            }
    
            $base64data = base64_decode($request->input('file'), true);
            $file_name = $request->input('user_id')."-".$request->input('file_name').time().".".strtolower($request->input('file_type'));
            
            $file_path  = "{$folder}{$file_name}";
    
            // Return the number of bytes saved, or false on failure
            $result = file_put_contents("{$file_path}", $base64data);
        }
        else{
            $file_name = NULL;
        }

        $other_file = OtherFile::create([
            'account_id' => $request->input('account_id'),
            'file_comment' => $request->input('file_comment'),
            'file_name' => $request->input('file_name'),
            'sources' => $file_name,
            'created_by' => auth('api')->id(),
            'updated_by' => auth('api')->id(),
        ]);
    }

    public function show($id)
    {
        //
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
