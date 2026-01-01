<?php

namespace App\Http\Controllers\Api\EMR\Consultation;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\ConsultationTrait;
use App\Http\Traits\Finance\TransactionTrait;
use App\Models\EMR\Consultation;
use Illuminate\Http\Request;

use App\Models\EMR\SpecialtyDoctor;
use App\Models\Staff;

class DashboardController extends Controller
{
    use ConsultationTrait, TransactionTrait;

    public function index()
    {
        return response()->json([
            'queue_mine' => $this->emr_consultation_get_all('mine', null, true, true, $_GET['page'] ?? 1), //$this->consultant_queue_mine(),
            //'queue_specialty' => $this->consultant_queue_my_specialties(),
            //'queue_doctor' => $this->consultant_queue_doctor(),
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
