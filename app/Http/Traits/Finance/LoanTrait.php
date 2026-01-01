<?php

namespace App\Http\Traits\Finance;

use App\Http\Traits\General\LogTrait;
use App\Http\Traits\Inventory\ItemTrait;
use App\Models\Operations\Branch;
use App\Models\EMR\Patient\Patient;
use App\Models\EMR\Patient\Insurance as PatientInsurance;
use App\Models\EMR\Visit;
use App\Models\Finance\Payment;
use App\Models\Finance\Transaction;
use App\Models\Finance\PriceList;
use App\Models\Finance\PriceListItem;
use App\Models\Finance\TopUp;
use App\Models\Insurance\PlanBranch;
use App\Models\Inventory\Item;
use App\Models\Operations\Branch as OperationBranch;
use App\Models\Operations\BranchPlanPriceList;
use App\Models\Procurement\Vendor;

use Exception;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait LoanTrait{

}