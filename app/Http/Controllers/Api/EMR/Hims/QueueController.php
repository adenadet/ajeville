<?php

namespace App\Http\Controllers\Api\EMR\Hims;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\ConsultationTrait;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    use ConsultationTrait;

    public function destroy($id)
    {
        return response()->json([
            
        ]);
    }

    public function dialysis()
    {
        return response()->json([
            'pending_requests' => $this->dialysis_queue_pending(),
            'requests' => $this->dialysis_queue(),
        ]);
    }

    public function doctor()
    {
        return response()->json([
            'doctor' => $this->queue_doctor(),
        ]);
    }

    public function index()
    {
        //
    }

    public function laboratory()
    {
        return response()->json([
            'pending_requests' => $this->laboratory_queue_pending(),
            'requests' => $this->laboratory_queue(),
        ]);
    }

    public function physio()
    {
        return response()->json([
            'pending_requests' => $this->physiotherapist_queue_pending(),
            'requests' => $this->physiotherapist_queue(),
        ]);
    }

    public function radiology()
    {
        return response()->json([
            'pending_requests' => $this->radiology_queue_pending(),
            'requests' => $this->radiology_queue(),
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

    public function vitals()
    {
        return response()->json([
            'pending_requests' => '',
            'requests' => $this->nurses_queue(),
        ]);
    }

}
