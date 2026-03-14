<?php 

namespace App\Services\EMR;

use App\Http\Traits\EMR\PatientTrait;
use App\Http\Traits\Ums\UserTrait;
use App\Models\EMR\Patient\Patient; 
use App\Models\EMR\VisitTransaction;
use App\Services\EMR\BillingService;
use App\Services\UMS\UserService;
use App\Services\UMS\UserRoleService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PatientService
{
    use PatientTrait, UserTrait;

    protected function actorId(): ?int
    {
        return auth('api')->id() ?? Auth::id();
    }


    public function createAndRegister($data): Patient
    {
        return DB::transaction(function () use ($data) {

            $reg_type = isset($data['reg_type']) ? $data['reg_type'] : Patient::TypeTemp;

            $user_service = new UserService();
            $user_role_service = new UserRoleService();
            $user = $user_service->create_user($data);
            $user_role_service->assign_role($user->id, 'Patient');
            
            $patient = Patient::create([
                'user_id' =>$user->id,
                'balance' =>0.00,
                'credit_limit' =>0.00,
                'patient_type' =>$reg_type,
                'unique_id' => config('app.short_code').'-'.date('YmdHis'),
                'old_emr_numbers' =>$data['old_emr_numbers'] ?? NULL,
                'blood_group' =>$data['blood_group'] ?? NULL,
                'genotype' =>$data['genotype'] ?? NULL,
                'occupation' =>$data['occupation'] ?? NULL,
                'referral_type_id' =>$data['referral_type_id'] ?? NULL,
                'referral_details' =>$data['referral_details'] ?? NULL,
                'other_details' =>$data['other_details'] ?? NULL,
                'created_by' => $this->actorId(),
                'updated_by' => $this->actorId(),
            ]);

            if (!empty($data['contacts']) && count($data['contacts']) != 0){
                foreach($data['contacts'] as $contact){
                    $patient_contact_service = new PatientContactService();
                    $patient_contact_service->create_contact($patient->id, $contact);
                }
            }
            if (!empty($data['insurances']) && count($data['insurances']) != 0){
                foreach($data['insurances'] as $insurance){
                    $patient_insurance_service = new PatientInsuranceService();
                    $patient_insurance_service->create($patient->id, $insurance);
                }
            }
            if (!empty($data['nok']) && count($data['nok']) != 0){
                $user_service->add_next_of_kin($user->id, $data['nok']);
            }
            
            $billing_manager = new BillingService();
            $billing_manager->createSpecialCharge('Registration', $patient->id, null, 1, true);
            return $patient;
        });
    }

    public function createTemporary($data): Patient
    {
        return DB::transaction(function () use ($data) {

            $reg_type = Patient::TypeTemp;

            $user_service = new UserService();
            $user_role_service = new UserRoleService();
            $user = $user_service->create_temporary_user($data);
            
            $patient = Patient::create([
                'user_id' =>$user->id,
                'balance' =>0.00,
                'credit_limit' =>0.00,
                'patient_type' =>$reg_type,
                'unique_id' => config('app.short_code').'-'.date('YmdHis'),
                'old_emr_numbers' =>$data['old_emr_numbers'] ?? NULL,
                'blood_group' =>$data['blood_group'] ?? NULL,
                'genotype' =>$data['genotype'] ?? NULL,
                'occupation' =>$data['occupation'] ?? NULL,
                'referral_type_id' =>$data['referral_type_id'] ?? NULL,
                'referral_details' =>$data['referral_details'] ?? NULL,
                'other_details' =>$data['other_details'] ?? NULL,
                'created_by' => $this->actorId(),
                'updated_by' => $this->actorId(),
            ]);

            $billing_manager = new BillingService();
            $billing_manager->createSpecialCharge('Registration', $patient->id, null, 1, true);
            return $patient;
        });
    }

    public function update(array $data, int $id): Patient
    {
        return DB::transaction(function () use ($data, $id) {

            $patient = Patient::findOrFail($id);

            $patient->fill([
                'blood_group' => $data['blood_group'] ?? $patient->blood_group,
                'genotype'    => $data['genotype'] ?? $patient->genotype,
                'occupation'  => $data['occupation'] ?? $patient->occupation,
                'updated_by'  => $this->actorId(),
            ])->save();

            $user_service = new UserService();
            $user_service->update_user($data, $patient->id);

            return $patient;
        });
    }

}
