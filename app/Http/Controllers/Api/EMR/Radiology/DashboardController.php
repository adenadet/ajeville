<?php

namespace App\Http\Controllers\Api\EMR\Radiology;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\RadiologyTrait;
use Illuminate\Http\Request;

use App\Models\EMR\RadiologyRequest;
use App\Models\EMR\Payment;
use App\Models\Finance\Transaction;

class DashboardController extends Controller
{
    use RadiologyTrait;
    public function index()
    {
        return response()->json([
            'new_request' => RadiologyRequest::where('date', '=', date('Y-m-d'))->count(),
            'cancelled_requests' => RadiologyRequest::where('status', '=', 0)->whereDate('date', '>=', date("Y-m-d", (strtotime("-1 month"))))->count(),
            'completed_requests' => RadiologyRequest::where('status', '=', 14)->whereDate('date', '>=', date("Y-m-d", (strtotime("-1 month"))))->count(),
            'completed_referred_in' => RadiologyRequest::where('status', '=', 14)->whereDate('date', '>=', date("Y-m-d", (strtotime("-1 month"))))->count(),
            'completed_referred_out' => RadiologyRequest::where('status', '=', 0)->whereDate('date', '>=', date("Y-m-d", (strtotime("-1 month"))))->count(),
            'new_requests' => RadiologyRequest::where('date', '=', date('Y-m-d'))->count(),
            'requests' => $this->emr_radiology_request_get_all('unconfirmed', [], true, true),
            'pending_referred_in' => RadiologyRequest::where('date', '=', date('Y-m-d'))->count(),
            'pending_referred_out' => RadiologyRequest::where('date', '=', date('Y-m-d'))->count(),
            'unpaid_requests' => RadiologyRequest::where('date', '=', date('Y-m-d'))->count(),
            'emergency_requests' => $this->emr_radiology_request_get_all('emergency', [], true, false),
        ]);
    }

    public function store(Request $request)
    {
        //
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
