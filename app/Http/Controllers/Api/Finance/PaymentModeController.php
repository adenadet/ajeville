<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
//use App\Http\Traits\Finance\PaymentTrait;
use App\Http\Traits\Finance\SettingTrait;
use Illuminate\Http\Request;

class PaymentModeController extends Controller
{
    //use PaymentTrait, SettingTrait;
        
    public function destroy($id)
    {
        $payment_mode = $this->finance_setting_payment_mode_deactivate($id);

        return response()->json([
            'message' => 'Payment mode deactivated/reactivated successfully',
            'payment_mode' => $payment_mode,
        ], is_string($payment_mode) ? 500 : 200);
    }


    public function index()
    {
        return response()->json([
            'payment_modes' => $this->finance_setting_payment_mode_get_all($_GET['status'] ?? 'active', $_GET, true, true, $_GET['page'] ?? 1),
        ]);
    }

    public function show($id)
    {
        $payment_mode = $this->finance_setting_payment_mode_get_by($id, true);

        return response()->json([
            'payment_mode' => $payment_mode,
        ], is_string($payment_mode) ? 404 : 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'status' => 'nullable|boolean',
        ]);

        $payment_mode = $this->finance_setting_payment_mode_create($data);

        return response()->json([
            'message' => 'Payment mode created successfully',
            'payment_mode' => $payment_mode,
        ], is_string($payment_mode) ? 500 : 201);
    }


    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'status' => 'nullable|boolean',
        ]);

        $payment_mode = $this->finance_setting_payment_mode_update($id, $data);

        return response()->json([
            'message' => 'Payment mode updated successfully',
            'payment_mode' => $payment_mode,
        ], is_string($payment_mode) ? 500 : 200);
    }
}
