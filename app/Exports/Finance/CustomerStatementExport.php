<?php

namespace App\Exports\Finance;

use Maatwebsite\Excel\Concerns\FromCollection;

class CustomerStatementExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
    }
}
