<?php

namespace App\Http\Traits\Finance;

use App\Http\Traits\General\LogTrait;
use App\Http\Traits\Inventory\ItemTrait;
use App\Models\CRM\Customer;
use App\Models\Operations\Branch;
use App\Models\EMR\Patient\Patient;
use App\Models\EMR\Patient\Insurance as PatientInsurance;
use App\Models\EMR\Visit;
use App\Models\Finance\Account;
use App\Models\Finance\BranchBank;
use App\Models\Finance\JournalEntry;
use App\Models\Finance\JournalLine;
use App\Models\Finance\MainTransaction;
use App\Models\Finance\Payment;
use App\Models\Finance\Transaction;
use App\Models\Finance\PriceList;
use App\Models\Finance\PriceListItem;
use App\Models\Finance\TopUp;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait TaxTrait{
    
}