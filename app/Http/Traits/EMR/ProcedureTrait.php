<?php

namespace App\Http\Traits\EMR;

use App\Http\Traits\General\LogTrait;
use App\Models\EMR\Consultation\Consultation;
use App\Models\EMR\Consultation\SpecialtyDoctor;
use App\Models\Operations\Branch;
use App\Models\EMR\Patient;
use App\Models\EMR\PatientInsurance;
use App\Models\EMR\Visit;
use App\Models\Inventory\Item;
use App\Models\Operations\BranchPlanPriceList;
use App\Models\Procurement\Vendor;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait ProcedureTrait{
    public function emr_procedure_request_get_all($type, $specific, $detailed, $paginated){

    }
}