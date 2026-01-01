<?php

namespace App\Http\Controllers\Api\Operations;

use App\Http\Controllers\Controller;
use App\Http\Traits\Operations\ModuleTrait;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    use ModuleTrait;
    public function index()
    {
        $modules = $this->operation_module_get_all($_GET['status'] ?? 'active', null, true, true, 20);

        return response()->json([
            'modules' => $modules
        ], is_string($modules) ? 500 : 200);
    }

    public function store(Request $request)
    {
        $modules = $this->operation_module_create($request);

        return response()->json([
            'modules' => $modules
        ], is_string($modules) ? 500 : 200);
    }

    public function show(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function userModules($user_id)
    {
        $user_modules = $this->operation_module_assigned_modules('user', $user_id);

        return response()->json([
            'modules' => $user_modules
        ], is_string($user_modules) ? 500 : 200);
    }
    
    public function destroy(string $id)
    {
        //
    }
}
