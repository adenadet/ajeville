<?php

namespace App\Http\Traits\Hrms;

use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\Hrms\Education;
use App\Models\Hrms\UserAccount;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait PreemployeeTrait{

    use FileManagerTrait, LogTrait;

    private function file_location(){
        return '/uploads/education/';
    }

    public function hrms_user_account_create($data){
        DB::beginTransaction();

        try{
            // safer: use null coalesce so missing 'file' or 'file_type' doesn't throw notice
            $file = null;
            if (!empty($data['file'])) {
                $file = $this->file_upload($data['file'], $data['file_type'] ?? null, $this->file_location(), null);
            }

            $query = Education::create([
                'user_id' => $data['user_id'],
                'qualification_id' => $data['qualification_id'],
                'details' => $data['details'] ?? null,
                'institution' => $data['institution'] ?? null,
                'address' => $data['address'] ?? null,
                'start_month' => $data['start_month'] ?? null,
                'end_month' => $data['end_month'] ?? null,
                'file' => $file ?? null,
                'file_type' => $data['file_type'] ?? null,
                'status' => Education::StatusUnverified,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);
            DB::commit();
            return $query;
        }
        catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function hrms_user_account_deactivate($id){}

    public function hrms_user_account_get_all($type, $specific, $detailed, $paginated){
        $query = Education::query();

        switch($type){
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'admin':
                //$query = $query->where();
            break;
            case 'unverified':
                $query = $query->where('status', '=', Education::StatusUnverified);
            break;
            case 'user':
                $query = $query->where('user_id', '=', $specific);
            break;
        }

        $query = $detailed ? $query->with(['user.applicant', 'user.employee', 'qualification']) : $query->select('id', 'qualification_id')->with(['qualification']);
        $query->orderBy('end_month', 'DESC');
        $query = $paginated ? $query->paginate(25) : $query->get();

        return $query;
    }

    public function hrms_user_account_get_by($type, $id, $detailed){
        try{
            $query = Education::where('id', '=', $id);

            $query = $detailed ? $query->with(['applicant', 'employee', 'qualification']) : $query->select('id', 'qualification_id')->with(['qualification']);

            return $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function hrms_user_account_make_primary($data, $id){
        DB::beginTransaction();
        try{
            $query = UserAccount::findOrFail($id);

            $quest = UserAccount::where('user_id', '=', $query->user_id)->update(['primary_account' => 0, 'updated_by' => auth('api')->id() ?? Auth::id()]);
            
            $query->primary_account = 1;
            $query->deleted_at = null;
            $query->deleted_by = null;
            $query->updated_by = auth('api')->id() ?? Auth::id();

            return $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }


    public function hrms_user_account_update($data, $id){
        // fixed typo here
        DB::beginTransaction();

        try{
            $file = null;
            // check for presence and not-null
            if (!empty($data['file'])) {
                $file = $this->file_upload($data['file'], $data['file_type'] ?? null, $this->file_location(), $id);
            }

            $query = Education::findOrFail($id);

            $query->user_id             = $data['user_id'] ?? $query->user_id;
            $query->qualification_id    = $data['qualification_id'] ?? $query->qualification_id;
            $query->details             = $data['details'] ?? $query->details;
            $query->institution         = $data['institution'] ?? $query->institution;
            $query->address             = $data['address'] ?? $query->address;
            $query->start_month         = $data['start_month'] ?? $query->start_month;
            $query->end_month           = $data['end_month'] ?? $query->end_month;
            if ($file !== null) {
                $query->file = $file;
            }
            $query->status              = Education::StatusUnverified;
            $query->updated_by          = Auth::id() ?? auth('api')->id();

            $query->save();

            DB::commit();
            return $query;
        }
        catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function hrms_user_education_approve($data, $id){}

    public function hrms_user_education_create($data){
        DB::beginTransaction();

        try{
            // safer: use null coalesce so missing 'file' or 'file_type' doesn't throw notice
            $file = null;
            if (!empty($data['file'])) {
                $file = $this->file_upload($data['file'], $data['file_type'] ?? null, $this->file_location(), null);
            }

            $query = Education::create([
                'user_id' => $data['user_id'],
                'qualification_id' => $data['qualification_id'],
                'details' => $data['details'] ?? null,
                'institution' => $data['institution'] ?? null,
                'address' => $data['address'] ?? null,
                'start_month' => $data['start_month'] ?? null,
                'end_month' => $data['end_month'] ?? null,
                'file' => $file ?? null,
                'file_type' => $data['file_type'] ?? null,
                'status' => Education::StatusUnverified,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);
            DB::commit();
            return $query;
        }
        catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function hrms_user_education_deactivate($id){}

    public function hrms_user_education_get_all($type, $specific, $detailed, $paginated){
        $query = Education::query();

        switch($type){
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'admin':
                //$query = $query->where();
            break;
            case 'unverified':
                $query = $query->where('status', '=', Education::StatusUnverified);
            break;
            case 'user':
                $query = $query->where('user_id', '=', $specific);
            break;
        }

        $query = $detailed ? $query->with(['user.applicant', 'user.employee', 'qualification']) : $query->select('id', 'qualification_id')->with(['qualification']);
        $query->orderBy('end_month', 'DESC');
        $query = $paginated ? $query->paginate(25) : $query->get();

        return $query;
    }

    public function hrms_user_education_get_by($type, $id, $detailed){
        try{
            $query = Education::where('id', '=', $id);

            $query = $detailed ? $query->with(['applicant', 'employee', 'qualification']) : $query->select('id', 'qualification_id')->with(['qualification']);

            return $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function hrms_user_education_update($data, $id){
        // fixed typo here
        DB::beginTransaction();

        try{
            $file = null;
            // check for presence and not-null
            if (!empty($data['file'])) {
                $file = $this->file_upload($data['file'], $data['file_type'] ?? null, $this->file_location(), $id);
            }

            $query = Education::findOrFail($id);

            $query->user_id             = $data['user_id'] ?? $query->user_id;
            $query->qualification_id    = $data['qualification_id'] ?? $query->qualification_id;
            $query->details             = $data['details'] ?? $query->details;
            $query->institution         = $data['institution'] ?? $query->institution;
            $query->address             = $data['address'] ?? $query->address;
            $query->start_month         = $data['start_month'] ?? $query->start_month;
            $query->end_month           = $data['end_month'] ?? $query->end_month;
            if ($file !== null) {
                $query->file = $file;
            }
            $query->status              = Education::StatusUnverified;
            $query->updated_by          = Auth::id() ?? auth('api')->id();

            $query->save();

            DB::commit();
            return $query;
        }
        catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
