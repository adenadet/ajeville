<?php

namespace App\Imports\Finance;

use App\Http\Traits\General\LogTrait;
use App\Models\Finance\PriceList;
use App\Models\Finance\PriceListItem;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PricelistImport implements ToModel, WithHeadingRow
{
    use LogTrait;
    public function model(array $row)
    {
        
    }

}
