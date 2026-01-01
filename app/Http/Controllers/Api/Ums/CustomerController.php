<?php

namespace App\Http\Controllers\Api\Ums;

use App\Http\Controllers\Controller;
use App\Models\Ums\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        //
    }
    public function search(string $id)
    {
        return response()->json([
            'customers' => Customer::where('first_name', 'LIKE', '%id%')->orWhere('last_name', 'LIKE', '%id%')->orderBy('first_name')->get(),
        ]);
    }

    public function show(string $id)
    {
        //
    }
    
    public function store(Request $request)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
