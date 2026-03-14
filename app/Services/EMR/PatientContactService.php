<?php

namespace App\Services\EMR;

use App\Models\EMR\Patient\Contact;
use Illuminate\Support\Facades\Auth;

class PatientContactService
{
    protected function actorId(): ?int
    {
        return Auth::id() ?? auth('api')->id();
    }

    public function create_contact(int $patientId, array $data): Contact
    {
        return Contact::create([
            'patient_id'    => $patientId,
            'name'          => $data['name'],
            'address'       => $data['address'] ?? null,
            'email_address' => $data['email'] ?? null,
            'phone'         => $data['phone'] ?? null,
            'created_by'    => $this->actorId(),
            'updated_by'    => $this->actorId(),
        ]);
    }

    public function deactivate_contact(int $id): Contact
    {
        $contact = Contact::findOrFail($id);

        $contact->update([
            'status'     => 0,
            'updated_by' => $this->actorId(),
        ]);

        return $contact;
    }
}