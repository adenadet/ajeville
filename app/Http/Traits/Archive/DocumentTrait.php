<?php
namespace App\Http\Traits\Archive;
use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\Archive\Category as ArchiveCategory;
use App\Models\Archive\Document;
use App\Models\EMR\Patient\Patient;
use App\Models\User;
use Exception;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait DocumentTrait{
    use FileManagerTrait, LogTrait;

    private function archive_document_unique_id_create($data){
        return config('app.short_code').'-'.dechex(time());
    }
    
    public function archive_document_cancel($id){
        DB::beginTransaction();

        try{
            $document = Document::where('id', '=', $id)->first();

            $transactions = $this->finance_transaction_get_all('document', $id, false, false, null);
            foreach ($transactions as $transaction){
                $this->finance_transaction_cancel($transaction->id);
            }

            $document->end_date = date('Y-m-d');
            $document->end_timestamp = date('Y-m-d H:i:s');
            $document->status = 2;
            $document->updated_by = auth('api')->id();

            $document->save();
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
    
    public function archive_document_create($data){
        DB::beginTransaction();

        try{
            $location = ArchiveCategory::find($data['category_id'])->location;
            $file_path = $this->file_upload($data['file'], $data['file_type'], $location, null);
            $document = Document::create([
                'unique_id' => $this->archive_document_unique_id_create($data),
                'patient_id' => $data['patient_id'],
                'category_id' => $data['category_id'],
                'file_type' => $data['file_type'],
                'file_name' => $data['file_name'],
                'file_path' => $file_path,
                'status' => 1,
                'created_by' => auth('api')->id(),
                'updated_by' => auth('api')->id(),
            ]);

            $this->log_create('document', $document->id, 'Document created', $document->unique_id, $document->branch_id, $document->patient_id, $document->document_type_id, $document->document_date, $document->document_number, $document->document_amount, $document->status, $document->created_by);

            DB::commit();
            return $document;
        }
        catch(Exception $e){
            DB::rollBack();
        }
    }

    public function archive_document_get_all($type, $specific, $detailed, $paginated, $page){}

    public function archive_document_get_by($type, $id, $detailed){}
    
    public function archive_document_search($data, $type, $detailed, $paginated, $page){
        $query = Document::where('status', '=', 1);
        if (isset($data['patient_name'])){
            $search = $data['patient_name'];
            $users = User::where('first_name', 'LIKE', "%$search%")
                ->orWhere('middle_name', 'LIKE', "%$search%")
                ->orWhere('last_name', 'LIKE', "%$search%")
                ->orWhere('email', 'LIKE', "%$search%")->get()->pluck('id');

            $patients = Patient::whereIn('user_id', $users)->get()->pluck('id');
            $query = $query->whereIn('patient_id', $patients);
        }
        if (isset($data['patient_id'])){
            $query = $query->where('patient_id', '=', $data['patient_id']);
        }
        if (isset($data['category_id'])){
            $query = $query->where('category_id', '=', $data['category_id']);
        }
        if (isset($data['sub_categories'])){
            $query = $query->whereIn('sub_category_id',  $data['sub_categories']);
        }
        if (isset($data['file_name'])){
            $search = $data['file_name'];
            $query = $query->where('file_name', 'LIKE', "%$search%");
        }
        if (isset($data['file_type'])){
            $query = $query->where('file_type', '=', $data['file_type']);
        }

        $query = $query->orderBy('created_at', 'DESC');

        $query = $detailed ? $query->with(['category', 'patient', 'sub_category']) : $query;

        $query = $paginated ? $query->paginate(50) : $query->get();

        return $query;
    }
    
    public function archive_document_update($data, $id){
        DB::beginTransaction();

        try{
            $location = ArchiveCategory::find($data['category_id'])->location;
            $file_path = $this->file_upload($data['file'], $data['file_type'], $location, null);
            $document = Document::create([
                'unique_id' => $this->archive_document_unique_id_create($data),
                'patient_id' => $data['patient_id'],
                'category_id' => $data['category_id'],
                'file_type' => $data['file_type'],
                'file_name' => $data['file_name'],
                'file_path' => $file_path,
                'status' => 1,
                'created_by' => auth('api')->id(),
                'updated_by' => auth('api')->id(),
            ]);

            $this->log_create('document', $document->id, 'Document created', $document->unique_id, $document->branch_id, $document->patient_id, $document->document_type_id, $document->document_date, $document->document_number, $document->document_amount, $document->status, $document->created_by);

            DB::commit();
            return $document;
        }
        catch(Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }
}