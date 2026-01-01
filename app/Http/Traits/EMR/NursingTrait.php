<?php
namespace App\Http\Traits\EMR;
use App\Http\Traits\General\FileTrait;
use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\Area;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Department;
use App\Models\EMR\Consultation\Consultation;
use App\Models\NextOfKin;
use App\Models\Staff;
use App\Models\State;
use App\Models\User;

use App\Models\EMR\Patient;
use App\Models\EMR\Nursing\Vital;
use App\Models\HRMS\Employee;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;


trait NursingTrait{
    use FileManagerTrait, LogTrait;

    private function nursing_vitals_calculate_coma_scale($data){
        $score = 0;

        $score += $data['gcs_eye'] ?? 4;
        $score += $data['gcs_verbal'] ?? 5;
        $score += $data['gcs_motor'] ?? 6;

        return $score;
    }

    private function nursing_vitals_calculate_news_score($vitals)
    {
        $score = 0;

        // Respiration rate
        $rr = $vitals['respiration_rate'];
        if ($rr <= 8) $score += 3;
        elseif ($rr >= 9 && $rr <= 11) $score += 1;
        elseif ($rr >= 21 && $rr <= 24) $score += 2;
        elseif ($rr >= 25) $score += 3;

        // SpO2
        $spo2 = $vitals['spo2'];
        if ($spo2 <= 91) $score += 3;
        elseif ($spo2 >= 92 && $spo2 <= 93) $score += 2;
        elseif ($spo2 >= 94 && $spo2 <= 95) $score += 1;

        // Blood Pressure (Systolic)
        $sbp = $vitals['bp_systolic'];
        if ($sbp <= 90) $score += 3;
        elseif ($sbp >= 91 && $sbp <= 100) $score += 2;
        elseif ($sbp >= 101 && $sbp <= 110) $score += 1;
        elseif ($sbp >= 220) $score += 3;

        // Temperature
        $temp = $vitals['temperature'];
        if ($temp <= 35) $score += 3;
        elseif ($temp >= 35.1 && $temp <= 36) $score += 1;
        elseif ($temp >= 39.1) $score += 2;

        // Pulse rate
        $pulse = $vitals['pulse'];
        if ($pulse <= 40) $score += 3;
        elseif ($pulse >= 41 && $pulse <= 50) $score += 1;
        elseif ($pulse >= 91 && $pulse <= 110) $score += 1;
        elseif ($pulse >= 111 && $pulse <= 130) $score += 2;
        elseif ($pulse > 130) $score += 3;

        // Consciousness (AVPU)
        if ($vitals['consciousness'] !== 'Alert') $score += 3;

        // Pain on movement (optional modifier or flag)
        // Glucose, GCS, pupils stored but not scored here

        return $score;
    }    

    public function nursing_vitals_create($data){
        DB::beginTransaction();

        try{
            $query = Vital::create([
                'patient_id' => $data['patient_id'], 
                'unique_id' => Str::uuid()->toString(),
                'consultation_id' => $data['consultation_id'] ?? null, 
                'temperature' => $data['temperature'] ?? null, 
                'blood_glucose' => $data['bloog_glucose'] ?? null, 
                'height' => $data['height'] ?? null, 
                'weight' => $data['weight'] ?? null, 
                'respiration_rate' => $data['respiration_rate'] ?? null, 
                'spo2' => $data['spo2'] ?? null, 
                'bp_systolic' => $data['bp_systolic'] ?? null, 
                'bp_diastolic' => $data['bp_diastolic'] ?? null, 
                'glasgow_score' => $this->nursing_vitals_calculate_coma_scale($data), 
                'heart_beat' => $data['heart_beat'] ?? null, 
                'news_score' => $this->nursing_vitals_calculate_news_score($data),
                'protein' => $data['protein'] ?? null, 
                'pulse' => $data['pulse'] ?? null, 
                'urine' => $data['urine'] ?? null, 
                'pain_on_movement' => $data['pain_on_movement'] ?? null, 
                'consciousness' => $data['consciousness'] ?? null, 
                'gcs_eye' => $data['gcs_eye'] ?? null, 
                'gcs_verbal' => $data['gcs_verbal'] ?? null, 
                'gcs_motor' => $data['gcs_motor'] ?? null, 
                'pupil_right_size' => $data['pupil_right_size'] ?? null, 
                'pupil_right_reaction' => $data['pupil_right_reaction'] ?? null, 
                'pupil_left_size' => $data['pupil_left_size'] ?? null, 
                'pupil_left_reaction' => $data['pupil_left_reaction'] ?? null, 
                'remarks' => $data['remarks'] ?? null, 
                'taken_by' => $data['taken_by'] ?? (Auth::id() ?? auth('api')->id()), 
                'visit_id' => $data['visit_id'] ?? null, 
                'created_by' => Auth::id() ?? auth('api')->id(), 
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);
            DB::commit();
            $this->user_log_activity('Patient Vital Create', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->user_log_activity('Patient Vital Update', null, true);
            return $e->getMessage();
        }
    }

    public function nursing_vitals_delete($type, $id){
        DB::beginTransaction();

        try{
            switch ($type){
                case 'id':
                    $query = Vital::where('id', '=', $id);
                break;
                case 'unique_id':
                    $query = Vital::where('unique_id', '=', $id);
                break;
            }

            $query->deleted_by = Auth::id() ?? auth('api')->id();
            $query->deleted_at = date('Y-m-d H:i:s');
            $query->save();
            
            if ($query->consultation_id != null){
                $consultation = Consultation::find($query->consultation_id);
                $consultation->vital_status = 0;
                $consultation->save();
            }
            DB::commit();
            $this->user_log_activity('Patient Vital Delete', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->user_log_activity('Patient Vital Delete', $id, false);
            return $e->getMessage();
        }
    }
    
    public function nursing_vitals_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'patient':
                $query = Vital::where('patient_id', '=', $specific);
                break;
            case 'visit':
                $query = Vital::where('visit_id', '=', $specific);
                break;
            case 'consultation':
                $query = Vital::where('consultation_id', '=', $specific);
                break;
            }
        $query = $detailed ? $query->with(['consultation', 'patient', 'visit']): $query->with(['patient']);
        $query->orderBy('created_at', 'desc')->get();

        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function nursing_vitals_get_by($type, $id, $detailed){
        switch ($type){
            case 'id':
                $query = Vital::where('id', '=', $id);
            break;
            case 'unique_id':
                $query = Vital::where('unique_id', '=', $id);
            break;
        }
        $query = $detailed ? $query->with(['consultation', 'patient', 'visit']): $query->with(['patient']);
        
        return $query->first();
    }

    public function nursing_vitals_update($data, $id){
        DB::beginTransaction();

        try{
            $query = Vital::findOrFail($id);
        
            $data['news_score'] = $this->nursing_vitals_calculate_news_score($data);
            $data['glasgow_score'] = $this->nursing_vitals_calculate_coma_scale($data); 
            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->update($data);

            DB::commit();
            $this->user_log_activity('Patient Vital Update', $id, true);
            return $query();
        }
        catch(Exception $e){
            DB::rollBack();
            $this->user_log_activity('Patient Vital Update', $id, false);
            return $e->getMessage();
        }
    }
}