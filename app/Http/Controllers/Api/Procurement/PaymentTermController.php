<?php

namespace App\Http\Controllers\Api\Procurement;

use App\Http\Controllers\Controller;
use App\Http\Traits\Procurement\SettingsTrait;
use Illuminate\Http\Request;

class PaymentTermController extends Controller
{
    use SettingsTrait;
    
    public function destroy(string $id)
    {
        $payment_term = $this->procurement_settings_payment_term_delete($id);
        
        return response()->json([
            'payment_term' => $payment_term,
        ], is_string($payment_term) ? 404 : 200);
    }

    public function index()
    {
        return response()->json([
            'payment_terms' => $this->procurement_settings_payment_term_get_all('all', null, true, true, $GET['page'] ?? 1),       
        ]);
    }

    
    public function show(string $id)
    {
        $payment_term = $this->procurement_settings_package_type_get_by('id', $id, true);
        
        return response()->json([
            'payment_term' => $payment_term,
        ]);
    }

    public function store(Request $request)
    {
        $payment_term = $this->procurement_settings_payment_term_create($request);
        
        return response()->json([
            'payment_term' => $payment_term,
        ], is_string($payment_term) ? 500 : 201);
    }

    public function update(Request $request, string $id)
    {
        $payment_term = $this->procurement_settings_package_type_update($request, $id);
        
        return response()->json([
            'payment_term' => $payment_term,
        ], is_string($payment_term) ? 500 : 201);
    }
}
