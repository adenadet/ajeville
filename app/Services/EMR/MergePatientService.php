<?php

namespace App\Services\EMR;

use App\Models\EMR\Patient\Patient;
use App\Models\EMR\Patient\Allergy;
use App\Models\EMR\VisitTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MergePatientService{
    /**
     * Merge source patient into target patient
     *
     * @throws \Throwable
     */
    public function merge(
        int $sourceId,
        int $targetId,
        string $reason,
        array $options = []
    ): Patient {

        return DB::transaction(function () use ($sourceId, $targetId, $reason, $options) {

            $source = Patient::with(['allergies', 'user'])
                ->lockForUpdate()
                ->findOrFail($sourceId);

            $target = Patient::with(['allergies', 'user'])
                ->lockForUpdate()
                ->findOrFail($targetId);

            $this->validateMerge($source, $target);

            $this->mergeRelations($source, $target);

            $this->mergeAllergies($source, $target);

            $this->mergeFinancials($source, $target);

            $this->mergeDemographics($source, $target);

            $this->handleUserMerge($source, $target, $options);

            $this->logMerge($source, $target, $reason);

            $this->deactivateSource($source, $target);

            //event(new \App\Events\PatientMergedEvent($source, $target));

            return $target->fresh();
        });
    }

    /* ------------------------------------------------------------
     |  VALIDATION
     |------------------------------------------------------------ */

    protected function validateMerge(Patient $source, Patient $target): void
    {
        if ($source->id === $target->id) {
            throw ValidationException::withMessages([
                'patient' => 'Cannot merge the same patient.'
            ]);
        }

        if ($source->merged_to_patient_id) {
            throw ValidationException::withMessages([
                'patient' => 'Source patient already merged.'
            ]);
        }

        if ($target->merged_to_patient_id) {
            throw ValidationException::withMessages([
                'patient' => 'Cannot merge into a merged patient.'
            ]);
        }

        if ($this->hasActiveAdmission($source)) {
            throw ValidationException::withMessages([
                'patient' => 'Cannot merge patient with active admission.'
            ]);
        }
    }

    protected function hasActiveAdmission(Patient $patient): bool
    {
        if (!method_exists($patient, 'admissions')) {
            return false;
        }

        return $patient->admissions()
            ->whereNull('discharged_at')
            ->exists();
    }

    /* ------------------------------------------------------------
     |  RELATION MERGE
     |------------------------------------------------------------ */

    protected function mergeRelations(Patient $source, Patient $target): void
    {
        $relations = [
            \App\Models\EMR\Consultation\Consultation::class,
            \App\Models\EMR\Laboratory\Request::class,
            \App\Models\EMR\Radiology\Request::class,
            \App\Models\EMR\Pharmacy\Prescription::class,
            VisitTransaction::class,
            \App\Models\EMR\Patient\Contact::class,
            \App\Models\EMR\Patient\Insurance::class,
        ];

        foreach ($relations as $modelClass) {
            if (class_exists($modelClass)) {
                $this->reassignPatientId($modelClass, $source->id, $target->id);
            }
        }
    }

    protected function reassignPatientId(string $modelClass, int $sourceId, int $targetId): void
    {
        /** @var Model $modelClass */
        $modelClass::where('patient_id', $sourceId)
            ->update(['patient_id' => $targetId]);
    }

    /* ------------------------------------------------------------
     |  ALLERGY MERGE (deduplicate)
     |------------------------------------------------------------ */

    protected function mergeAllergies(Patient $source, Patient $target): void
    {
        foreach ($source->allergies as $allergy) {

            $exists = $target->allergies()
                ->where('substance', $allergy->substance)
                ->exists();

            if (!$exists) {
                $allergy->patient_id = $target->id;
                $allergy->save();
            } else {
                $allergy->delete();
            }
        }
    }

    /* ------------------------------------------------------------
     |  FINANCIAL MERGE
     |------------------------------------------------------------ */

    protected function mergeFinancials(Patient $source, Patient $target): void
    {
        $target->balance = $target->balance + $source->balance;
        $target->credit_limit = max(
            $target->credit_limit ?? 0,
            $source->credit_limit ?? 0
        );

        $target->save();
    }

    /* ------------------------------------------------------------
     |  DEMOGRAPHIC MERGE
     |------------------------------------------------------------ */

    protected function mergeDemographics(Patient $source, Patient $target): void
    {
        $fields = [
            'blood_group',
            'genotype',
            'occupation',
            'referral_type_id',
            'referral_details',
            'other_details',
        ];

        foreach ($fields as $field) {
            if (empty($target->$field) && !empty($source->$field)) {
                $target->$field = $source->$field;
            }
        }

        $target->save();
    }

    /* ------------------------------------------------------------
     |  USER HANDLING
     |------------------------------------------------------------ */

    protected function handleUserMerge(
        Patient $source,
        Patient $target,
        array $options
    ): void {

        $keepTargetUser = $options['keep_target_user'] ?? true;

        if (!$source->user) {
            return;
        }

        if (!$target->user) {
            $target->user_id = $source->user_id;
            $target->save();
            return;
        }

        if ($keepTargetUser) {
            $source->user->delete(); // soft delete
        } else {
            $target->user->delete();
            $target->user_id = $source->user_id;
            $target->save();
        }
    }

    /* ------------------------------------------------------------
     |  AUDIT LOG
     |------------------------------------------------------------ */

    protected function logMerge(
        Patient $source,
        Patient $target,
        string $reason
    ): void {

        DB::table('emr_patient_merges')->insert([
            'source_patient_id' => $source->id,
            'target_patient_id' => $target->id,
            'merged_by' => Auth::id(),
            'reason' => $reason,
            'created_at' => now(),
        ]);
    }

    /* ------------------------------------------------------------
     |  DEACTIVATE SOURCE
     |------------------------------------------------------------ */

    protected function deactivateSource(
        Patient $source,
        Patient $target
    ): void {

        $source->merged_to_patient_id = $target->id;
        $source->merged_at = now();
        $source->merged_by = Auth::id();
        $source->save();
    }

    public function preview(int $sourceId, int $targetId): array
    {
        $source = Patient::withCount([
            'allergies',
            'transactions',
            'contacts',
            'insurances'
        ])->findOrFail($sourceId);

        $target = Patient::withCount([
            'allergies',
            'transactions',
            'contacts',
            'insurances'
        ])->findOrFail($targetId);

        return [
            'source' => $source,
            'target' => $target,
            'impact' => [
                'transactions_to_move' => $source->transactions_count,
                'allergies_to_reconcile' => $source->allergies_count,
                'contacts_to_move' => $source->contacts_count,
                'insurances_to_move' => $source->insurances_count,
            ]
        ];
    }
}