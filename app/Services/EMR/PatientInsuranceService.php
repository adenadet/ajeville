<?php

namespace App\Services\EMR;

use App\Models\EMR\Patient\Insurance;
use Illuminate\Support\Facades\Auth;

class PatientInsuranceService
{
    protected function actorId(): ?int
    {
        return Auth::id() ?? auth('api')->id();
    }

    public function create(int $patientId, array $data): Insurance
    {
        return Insurance::create([
            'patient_id'   => $patientId,
            'provider_id'  => $data['provider_id'],
            'plan_id'      => $data['id'],
            'enrollee_id'  => $data['enrollee_id'] ?? null,
            'expiry_date'  => $data['expiry_date'] ?? null,
            'other_details'=> $data['other_details'] ?? null,
            'status'       => 1,
            'created_by'   => $this->actorId(),
            'updated_by'   => $this->actorId(),
        ]);
    }

    public function deactivate(int $id): Insurance
    {
        $insurance = Insurance::findOrFail($id);

        $insurance->update([
            'status'     => 0,
            'updated_by' => $this->actorId(),
        ]);

        return $insurance;
    }
}