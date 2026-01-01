<?php

namespace App\Http\Controllers\Api\Operations;

use App\Http\Controllers\Controller;
use App\Http\Traits\Operations\BranchTrait;
use Illuminate\Http\Request;

use App\Models\Operations\Branch;
use App\Models\Finance\PriceList;
use App\Models\HRMS\Employee;
use App\Models\Operations\BranchModule;
use App\Models\Operations\Module;
use App\Models\User;


class BranchController extends Controller
{
    use BranchTrait;
    public function index()
    {
        return response()->json([
            'branches'    => $this->operation_branch_get_all(true, true, $_GET['page'] ?? 1)
        ]);        
    }

    public function initials()
    {
        return response()->json([
            'branches'      => Branch::where('status', '=', 1)->get(),
            'employees'     => Employee::whereNotNull('user_id')->where('user_id', '!=', 0)->has('user')->orderBy('username', 'ASC')->with(['user'])->get(),
            'modules'       => Module::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'price_lists'   => PriceList::where('status', '=', 1)->orderBy('name', 'ASC')->get(),             
        ]);        
    }
    public function store(Request $request)
    {
        $branch = $this->operation_branch_create($request);

        return response()->json([
            'branches'    => $this->operation_branch_get_all(true, true, $_GET['page'] ?? 1)
        ], is_string($branch) ? 500 : 201);
    }

    public function show($id)
    {
        return response()->json([
            'branch'    => $this->operation_branch_get_branch_by_id($id, true),
            'users'     => User::all(),       
        ]);
    }

    public function update(Request $request, $id)
    {
        $branch = $this->operation_branch_update_branch($request, $id);
        return response()->json([
            'branches'    => Branch::with(['chief_consultant.user', 'head_nurse.user', 'practice_manager.user', 'modules', 'price_list'])->orderBy('name', 'ASC')->paginate(10),       
        ]);
    }

    public function destroy($id)
    {
        $branch = $this->operation_branch_delete($id);

        return response()->json([
            'branches'    => Branch::with(['chief_consultant.user', 'head_nurse.user', 'practice_manager.user', 'modules'])->orderBy('name', 'ASC')->paginate(10),
            'message'   => $branch->status == 1 ? 'Reactivated Successfully' : 'Deactivated Successfully',
            'notification' => 'Done', 
        ]); 
    }

    public function get_cookie()
    {
        $branch_id = request()->cookie('current_branch');
        return response()->json([
            'branch' => Branch::where('id', '=', $branch_id)->with('price_list.price_list_items')->first(),
            'user' => User::with('branch')->where('id', '=', auth('api')->id())->first(),
        ]);
    }

    public function set_cookie(Request $request)
    {
        $branch = $request->input('branch');
        $cookie = cookie('current_branch', $branch['id'], 60*60*12); // Name, Value, Minutes
        return response('Cookie has been set')->cookie($cookie);
    }
}
