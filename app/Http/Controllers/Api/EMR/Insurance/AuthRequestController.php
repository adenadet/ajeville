<?php

namespace App\Http\Controllers\Api\EMR\Insurance;

use App\Http\Controllers\Controller;
use App\Models\Insurance\AuthRequest;
use Illuminate\Http\Request;

class AuthRequestController extends Controller
{
    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        foreach ($request->input('transactions') as $transaction)
            AuthRequest::create([
                'transaction_id' => $transaction['id'],
                'request_method' => $request->input('request_method'),
                'contact_person' => $request->input('contact_person'),
                'phone' => $request->input('phone'),
                'email' => $request->input('email'),
                'plan_id' => $request->input('plan_id'),
                'description' => $request->input('description'),
                'created_by' => auth('api')->id(),
                'updated_by' => auth('api')->id(),
            ]);
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request,
    $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
