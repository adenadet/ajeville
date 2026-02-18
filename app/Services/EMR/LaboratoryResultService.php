<?php 

namespace App\Services\EMR;

use App\Models\EMR\Laboratory\ReferenceRange;
use App\Models\EMR\Laboratory\Request;
use App\Models\EMR\Laboratory\RequestDetail;
use App\Models\EMR\Laboratory\Result as LaboratoryResult;
use App\Models\EMR\Laboratory\ResultVersion;
use Exception;
use Illuminate\Support\Facades\DB;


class LaboratoryResultService
{
    public function amend($result, $newValue, $reason)
    {
        DB::transaction(function () use ($result, $newValue, $reason) {

            ResultVersion::create([
                'result_id' => $result->id,
                'old_value' => $result->value_numeric,
                'new_value' => $newValue,
                'changed_by' => auth()->id(),
                'reason' => $reason,
            ]);

            $result->update([
                'value_numeric' => $newValue,
                'status' => LaboratoryResult::StatusAmended,
            ]);
        });
    }

    protected function calculateAgeInDays($patient){
        return $patient->date_of_birth ? $patient->date_of_birth->diffInDays(now()) : null;
    }

    protected function calculateFlag($value, $range)
    {
        if ($value < $range['critical_low']) return 'CL';
        if ($value > $range['critical_high']) return 'CH';
        if ($value < $range['low']) return 'L';
        if ($value > $range['high']) return 'H';

        return 'N';
    }

    public function enterValue($result, $value)
    {
        if ($result->status !== LaboratoryResult::StatusPending) {
            throw new Exception("Result already entered.");
        }

        $range = $this->resolveReferenceRange($result);

        $flag = $this->calculateFlag($value, $range);

        $result->update([
            'value_numeric' => $value,
            'unit_snapshot' => $range['unit'],
            'reference_range_snapshot' => json_encode($range),
            'flag' => $flag,
            'entered_by' => auth()->id(),
            'entered_at' => now(),
            'status' => LaboratoryResult::StatusEntered
        ]);
    }

    protected function resolveReferenceRange($result)
    {
        $detail = $result->request_item;
        $request = $detail->request;
        $patient = $request->patient;
        $analyte = $result->analyte;

        $ageInDays = $this->calculateAgeInDays($patient);
        $gender = strtolower($patient->gender);

        $query = ReferenceRange::query()
            ->where('analyte_id', $analyte->id)
            ->where(function ($q) use ($gender) {
                $q->where('gender', $gender)
                ->orWhere('gender', 'both')
                ->orWhereNull('gender');
            });

        if ($ageInDays !== null) {
            $query->where(function ($q) use ($ageInDays) {
                $q->whereNull('age_min')
                ->orWhere('age_min', '<=', $ageInDays);
            })
            ->where(function ($q) use ($ageInDays) {
                $q->whereNull('age_max')
                ->orWhere('age_max', '>=', $ageInDays);
            });
        }

        $range = $query->orderByDesc('age_min')->first();

        if (! $range) {
            throw new \Exception("Reference range not configured for {$analyte->name}");
        }

        return [
            'low' => $range->low_value,
            'high' => $range->high_value,
            'critical_low' => $range->critical_low,
            'critical_high' => $range->critical_high,
            'unit' => $range->unit,
            'range_id' => $range->id
        ];
    }

}
