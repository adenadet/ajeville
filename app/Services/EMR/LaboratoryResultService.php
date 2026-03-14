<?php 

namespace App\Services\EMR;

use App\Models\EMR\Laboratory\ReferenceRange;
use App\Models\EMR\Laboratory\Request as LaboratoryRequest;
use App\Models\EMR\Laboratory\RequestDetail;
use App\Models\EMR\Laboratory\Result as LaboratoryResult;
use App\Models\EMR\Laboratory\ResultTemplateVersion;
use App\Models\EMR\Laboratory\ResultValue;
use App\Models\EMR\Laboratory\ResultVersion;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class LaboratoryResultService
{
    public function amend($result, $values, $reason)
    {
        return DB::transaction(function () use ($result, $values, $reason) {

            $latest = $result->latestVersion;

            $version = ResultVersion::create([
                'result_id' => $result->id,
                'template_version_id' => $latest->template_version_id,
                'version_number' => $latest->version_number + 1,
                'status' => ResultVersion::StatusDraft,
                'amend_reason' => $reason,
                'created_by' => auth()->id()
            ]);

            foreach ($latest->values as $oldValue) {

                $newValue = collect($values)->firstWhere('analyte_id', $oldValue->analyte_id);
 
                ResultValue::create([
                    'result_version_id' => $version->id,
                    'analyte_id' => $oldValue->analyte_id,
                    'value' => $newValue['value'] ?? $oldValue->value,
                    'unit' => $oldValue->unit,
                    'reference_range' => $oldValue->reference_range,
                    'flag' => $oldValue->flag
                ]);
            }

            $result->update([
                'status' => LaboratoryResult::StatusAmended
            ]);

            return $version;
        });
    }

    protected function calculateAgeInDays($patient){
        return $patient->date_of_birth ? $patient->date_of_birth->diffInDays(now()) : null;
    }

    protected function calculateFlag($value, $range)
    {
        if ($value < $range['critical_low']) return 'CL';
        if ($value > $range['low'] && $value <= $range['normal']) return '';
        if ($value < $range['low']) return 'L';
        if ($value > $range['high']) return 'H';
        return 'N';
    }

    public function create($data)
    {
        return DB::transaction(function () use ($data) {
            $request = LaboratoryRequest::lockForUpdate()->findOrFail($data['request_id']);

            $result = $data['result_id'] ? LaboratoryResult::findOrFail($data['result_id'])
            :LaboratoryResult::create([
                'request_id' => $data['request_id'],
                'status' => LaboratoryResult::StatusPending,
                'entered_by' => $data['entered_by'] ?? auth('api')->id() ?? Auth::id(),
                'entered_at' => now(),
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            $versions = ResultVersion::where('result_id', '=', $result->id)->get()->count();

            $version = ResultVersion::create([
                'result_id' => $result->id,
                'version_number' => $versions++,
                'status' => ResultVersion::StatusDraft,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            foreach($data['values'] as $result_value){
                ResultValue::create([
                    'result_version_id' => $version->id,
                    'analyte_id' => $result_value['analyte_id'],
                    'analyte_name' => $result_value['analyte_name'],
                    'specimen_id' => $result_value['specimen_id'],
                    'value' => $result_value['value'],
                    'unit' => $result_value['unit'],
                    'reference' => $result_value['reference_low']." ".$result_value['reference_normal'],
                    'reference_range' => [
                        'low' => $result_value['reference_low'],
                        'normal' => $result_value['reference_normal'],
                        'high' => $result_value['reference_high'],
                        'critical_low' => $result_value['reference_critical_low'],
                    ],
                    'flag' => $result_value['flag'],
                    'created_by' => auth('api')->id() ?? Auth::id(),
                    'updated_by' => auth('api')->id() ?? Auth::id(),
                ]);
            }

            $version->update([
                'status' => ResultVersion::StatusSubmitted,
            ]);

            $result->update([
                'status' => LaboratoryResult::StatusEntered,
            ]);

            $request->update([
                'status' => LaboratoryRequest::StatusOngoing,
                'updated_by' => auth('api')->id() ?? Auth::id()
            ]);
            $request->save();

            return $result->load('versions.values');
        });
    }

    public function initial($request){
        $request->load(['patient.user', 'lab_service.service_analytes.analyte.reference_ranges']);
        $dob = $request->patient->user->dob;
        $age = $dob ? $dob->diffInDays(now()) : 6570;
        $gender = $request->patient->user->sex;
        
        $analytes = $request->lab_service?->service_analytes?->map(function ($item) use ($age, $gender) {
            $range = $item->analyte->resolveReferenceRange($age,$gender);

            return [
                'id' => $item->id,
                'analyte_id' => $item->analyte_id,
                'analyte_name' => $item->analyte->name,
                'unit' => $item->analyte->default_unit,
                'specimen_id' => null,
                'reference_critical_low' => $range?->critical_low,
                'reference_low' => $range?->low_value,
                'reference_normal' => $range?->normal_value,
                'reference_high' => $range?->high_value,
                'reference_range' => $range ? "{$range->low_value} - {$range->normal_value}" : null,
                'flag' => null
            ];
        });

        return $analytes;
    }

    public function initialize(LaboratoryResult $result)
    {
        return DB::transaction(function () use ($result) {
            $age = $result->request->patient->dob->diffInDays(now());
            $gender = $result->request->patient->gender;
            $template = ResultTemplateVersion::with('analytes.analyte.reference_ranges')->findOrFail($result->template_version_id);

            $version = ResultVersion::create([
                'result_id' => $result->id,
                'template_version_id' => $template->id,
                'version_number' => 1,
                'status' => ResultVersion::StatusDraft,
                'created_by' => auth()->id()
            ]);

            foreach ($template->analytes as $analyte) {
                $range = $analyte->analyte->resolveReferenceRange($age,$gender);
                ResultValue::create([
                    'result_version_id' => $version->id,
                    'analyte_id' => $analyte->analyte_id,
                    'unit' => $range->unit,
                    'reference_range' => json_encode([
                        'low'=>$range->low_value,
                        'normal'=> $range->normal_value,
                        'high'=>$range->high_value,
                        'critical_low'=>$range->critical_low,
                    ])
                ]);
            }

            return $version;
        });
    }

    public function release(LaboratoryResult $result)
    {
        return DB::transaction(function () use ($result) {

            if ($result->status !== LaboratoryResult::StatusEntered) {
                throw new Exception("Result is not ready for release.");
            }

            $result->update([
                'status' => LaboratoryResult::StatusReleased,
                'released_at' => now(),
                'released_by' => auth('api')->id() ?? Auth::id(),
            ]);

            return $result;
        });
    }

    public function saveValues(ResultVersion $version, $values)
    {
        DB::transaction(function () use ($version, $values) {
            foreach ($values as $item) {
                $value = $version->values()->findOrFail($item['id']);  
                $value->update([
                    'value' => $item['value'],
                    'flag' => $item['flag'],
                    'updated_by' => auth('api')->id() ?? Auth::id(),
                ]);
            }

            $version->update([
                'status' => ResultVersion::StatusSubmitted
            ]);

            $version->result->update([
                'status' => LaboratoryResult::StatusEntered
            ]);

        });
    }

    /* public function verify($result, $decision, $comment = null, $reason = null)
    {
        DB::transaction(function () use ($result, $decision, $comment, $reason) {

            if ($decision === 'approve') {

                $result->update([
                    'status' => LaboratoryResult::StatusReleased,
                    'released_by' => auth('api')->id() ?? Auth::id(),
                    'released_at' => now(),
                    'verified_by' => auth('api')->id() ?? Auth::id(),
                    'verified_at' => now(),
                ]);

                $result->request->update([
                    'status' => LaboratoryRequest::StatusConfirmed, 
                    'updated_by' => auth('api')->id() ?? Auth::id(),
                ]);

                $result->latest_version->update([
                    'status' => ResultVersion::StatusCompleted,
                    'updated_by' =>auth('api')->id() ?? Auth::id(),
                ]);
            } 
            else {
                $result->update([
                    'status' => LaboratoryResult::StatusSecondaryReview
                ]);
            }

            $result->reviews()->create([
                'decision' => $decision,
                'reason' => $reason,
                'comment' => $comment,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);
        });
    }
 */
    public function verify($result, $decision, $comment = null, $reason = null)
    {
        $userId = auth('api')->id() ?? Auth::id();
        DB::transaction(function () use ($result, $decision, $comment, $reason, $userId) {
            // Ensure relationships are loaded
            $result->load(['request', 'latestVersion']);

            //echo $result->status;
            /*if ($result->status !== LaboratoryResult::StatusEntered && $result->status !== LaboratoryResult::StatusAmended) {
                throw new Exception('Result cannot be verified in current state');
            }*/
            if ($decision === 'confirm') {
                // 1. Update Result
                $result->update([
                    'status'      => LaboratoryResult::StatusReleased,
                    'verified_by' => $userId,
                    'verified_at' => now(),
                    'released_by' => $userId,
                    'released_at' => now(),
                    'updated_by'  => $userId,
                ]);

                // 2. Update Result Version
                if ($result->latestVersion) {
                    $result->latestVersion->update([
                        'status'     => ResultVersion::StatusCompleted,
                        'updated_by' => $userId,
                    ]);
                }

                // 3. Update Laboratory Request
                if ($result->request) {
                    $result->request->update([
                        'status'     => LaboratoryRequest::StatusConfirmed,
                        'updated_by' => $userId,
                    ]);
                }

            } else {

                // Send for secondary review
                $result->update([
                    'status'     => LaboratoryResult::StatusSecondaryReview,
                    'updated_by' => $userId,
                ]);
            }

            // Record review
            $result->reviews()->create([
                'decision'   => $decision,
                'reason'     => $reason,
                'comment'    => $comment,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);
        });
    }
}
