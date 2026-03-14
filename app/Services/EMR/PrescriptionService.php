<?php

namespace App\Services\EMR;

use App\Models\EMR\Patient\Patient;
use App\Models\EMR\Pharmacy\Prescription;
use App\Models\EMR\Pharmacy\PrescriptionDrug;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class PrescriptionService
{
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $patient_manager = new PatientService();

            $patient = !empty($data['patient_id']) ? Patient::findOrFail($data['patient_id']) : $patient_manager->create($data['patient']);

            $prescription = Prescription::create([
                'patient_id'        => $data['patient_id'] ?? null,
                'consultation_id'   => $data['consultation_id'] ?? null,
                'doctor_name'       => $data['doctor_name'] ?? null,
                'prescription_date' => $data['prescription_date'] ?? now(),
                'notes'             => $data['notes'] ?? null,
                'status'            => $data['status'] ?? 'active',
                'created_by'        => auth()->id(),
            ]);

            if (!empty($data['drugs'])) {
                $this->createDrugs($prescription, $data['drugs']);
            }

            return $prescription->load(['drugs']);
        });
    }

    public function getAll(array $filters = []): Collection
    {
        $query = Prescription::with(['drugs', 'patient', 'consultation'])->latest();

        if (!empty($filters['patient_id'])) {
            $query->where('patient_id', $filters['patient_id']);
        }

        if (!empty($filters['consultation_id'])) {
            $query->where('consultation_id', $filters['consultation_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->get();
    }

    public function find(int $id)
    {
        return Prescription::with(['drugs', 'patient', 'consultation'])->findOrFail($id);
    }

    /**
     * Update prescription
     */
    public function update(int $id, array $data): Prescription
    {
        return DB::transaction(function () use ($id, $data) {

            $prescription = $this->find($id);

            $prescription->update([
                'doctor_name'       => $data['doctor_name'] ?? $prescription->doctor_name,
                'prescription_date' => $data['prescription_date'] ?? $prescription->prescription_date,
                'notes'             => $data['notes'] ?? $prescription->notes,
                'status'            => $data['status'] ?? $prescription->status,
            ]);

            if (isset($data['drugs'])) {
                $this->syncDrugs($prescription, $data['drugs']);
            }

            return $prescription->load(['drugs']);
        });
    }

    /**
     * Delete prescription safely
     */
    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {

            $prescription = $this->find($id);

            $prescription->drugs()->delete();
            $prescription->delete();
        });
    }

    /**
     * Mark prescription fulfilled (pharmacy action)
     */
    public function markFulfilled(int $id): Prescription
    {
        $prescription = $this->find($id);

        $prescription->update([
            'status' => 'fulfilled',
            'fulfilled_at' => now()
        ]);

        return $prescription;
    }

    /**
     * Mark prescription partially fulfilled
     */
    public function markPartiallyFulfilled(int $id): Prescription
    {
        $prescription = $this->find($id);

        $prescription->update([
            'status' => 'partially_fulfilled'
        ]);

        return $prescription;
    }

    /**
     * Expire prescription
     */
    public function markExpired(int $id): Prescription
    {
        $prescription = $this->find($id);

        $prescription->update([
            'status' => 'expired'
        ]);

        return $prescription;
    }

    /**
     * Create Prescription Drugs
     */
    protected function createDrugs(Prescription $prescription, array $drugs): void
    {
        foreach ($drugs as $drug) {

            PrescriptionDrug::create([
                'prescription_id' => $prescription->id,
                'drug_id'         => $drug['drug_id'],
                'dosage'          => $drug['dosage'] ?? null,
                'frequency'       => $drug['frequency'] ?? null,
                'duration'        => $drug['duration'] ?? null,
                'quantity'        => $drug['quantity'] ?? null,
                'instructions'    => $drug['instructions'] ?? null,
            ]);
        }
    }

    protected function syncDrugs(Prescription $prescription, array $drugs): void
    {
        $prescription->drugs()->delete();

        $this->createDrugs($prescription, $drugs);
    }
}
