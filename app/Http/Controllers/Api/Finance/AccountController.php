<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function destroy(string $id)
    {
        //
    }
    
    public function index() {
        return Account::all();
    }

    public function show(string $id)
    {
        //
    }

    
    public function store(Request $request) {
        $request->validate([
            'code' => 'required|unique:accounts',
            'name' => 'required',
            'type' => 'required|in:Asset,Liability,Equity,Revenue,Expense',
        ]);

        
        return Account::create($request->all());
    }

    public function update(Request $request, string $id)
    {
        //
    }

}
