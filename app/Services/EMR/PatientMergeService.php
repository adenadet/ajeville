<?php

namespace App\Services\EMR;

use App\Models\EMR\Patient\Allergy;
use App\Models\EMR\Patient\Patient;
use App\Models\EMR\VisitTransaction;
use App\Models\EMR\Pharmacy\Prescription;
use App\Models\EMR\Laboratory\Request as LaboratoryRequest;
use App\Models\EMR\Radiology\Request as RadiologyRequest;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PatientMergeService
{
    protected function actorId(): ?int
    {
        return Auth::id() ?? auth('api')->id();
    }

    protected function deactivateSource($source, $target){
        $source->merged_to_patient_id = $target->id;
        $source->merged_at = now();
        $source->merged_by = $this->actorId();
        $source->save();
    }

    protected function mergeDemographics($source, $target){
        $fields = [
            'blood_group',
            'genotype',
            'occupation',
            'referral_type_id',
            'referral_details',
            'other_details'
        ];

        foreach ($fields as $field) {
            if (!$target->$field && $source->$field) {
                $target->$field = $source->$field;
            }
        }

        $target->save();
    }

    protected function mergeFinancials($source, $target){
        $target->balance += $source->balance;
        $target->credit_limit = max($target->credit_limit, $source->credit_limit);
        $target->save();
    }

    protected function reassign($modelClass, $sourceId, $targetId)
    {
        $modelClass::where('patient_id', $sourceId)->update(['patient_id' => $targetId]);
    }

    protected function validateMerge($source, $target){
        if ($source->id === $target->id) {throw new Exception('Patient is the same');}
        if ($source->merged_to_patient_id) {throw new Exception('Patient has alrerady been merged');}
    }

    private function mergeRelations($source, $target){
        $this->reassign(Allergy::class, $source->id, $target->id);
        $this->reassign(VisitTransaction::class, $source->id, $target->id);
        $this->reassign(LaboratoryRequest::class, $source->id, $target->id);
        $this->reassign(RadiologyRequest::class, $source->id, $target->id);
        $this->reassign(Prescription::class, $source->id, $target->id);
    }

    public function merge($sourceId, $targetId): Patient
    {
        return DB::transaction(function () use ($sourceId, $targetId) {

            $source = Patient::lockForUpdate()->where('id', '=', $sourceId)->orWhere('unique_id', '=', $sourceId)->firstOrFail();
            $target = Patient::lockForUpdate()->where('id', '=', $targetId)->orWhere('unique_id', '=', $targetId)->firstOrFail();

            $this->validateMerge($source, $target);

            $this->mergeRelations($source, $target);

            $this->mergeFinancials($source, $target);

            $this->mergeDemographics($source, $target);

            $this->deactivateSource($source, $target);

            return $target->fresh();
        });
    }
}