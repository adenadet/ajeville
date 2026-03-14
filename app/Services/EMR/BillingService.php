<?php 

namespace App\Services\EMR;

use App\Http\Traits\EMR\VisitTransactionTrait;
use App\Models\EMR\Admission\Request as AdmissionRequest;
use App\Models\EMR\Admission\BedAssignment;
use App\Models\EMR\Patient\Patient;
use App\Models\EMR\VisitTransaction;
use App\Models\Inventory\Item;

class BillingService
{
    use VisitTransactionTrait;
    public function createSpecialCharge($type, $patient_id, $visit_id = null, $num = 1, $auto_debit = false)
    {
        switch($type){
            case 'Registration':
                $item = Item::where('name', '=', 'Registration')->first();
            break;
        }
        
        return $this->emr_visit_transaction_create(
            $item->id,
            $patient_id,
            $num,
            $auto_debit,
            $visit_id
        );
    }
}