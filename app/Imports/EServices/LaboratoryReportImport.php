<?php

namespace App\Imports\EServices;

use App\Http\Traits\General\LogTrait;
use App\Models\EMR\Appointment;
use App\Models\EMR\Laboratory;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LaboratoryReportImport implements ToModel, WithHeadingRow
{
    use LogTrait;
    public function model(array $row)
    {
        $appointment = (is_integer($row['appointment_id']))? Appointment::where('id', '=', $row['appointment_id'])->first() : Appointment::where('unique_id', '=', $row['appointment_id'])->first();
        $laboratory_report = Laboratory::updateOrCreate(['appointment_id' => $appointment->id],
        [
            'patient_id' => $appointment->patient_id, 
            'summary' => $row['summary'], 
            'details' => $row['details'],
            'updated_by' => Auth::id() ?? auth('api')->id()
        ]);

        $appointment->lab_officer_id = Auth::id() ?? auth('api')->id();
        $appointment->lab_officer_remark = $row['details'];
        $appointment->lab_at = date('Y-m-d H:i:s');
        $appointment->updated_by = Auth::id() ?? auth('api')->id();
        $appointment->updated_at = date('Y-m-d H:i:s');
        
        $appointment->save();

        $this->log_user_activity('New Laboratory Report Created', $laboratory_report->id, true);
        return $laboratory_report;
    }

}
