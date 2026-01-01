<?php
namespace App\Http\Traits\Archive;
use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\Archive\Category as ArchiveCategory;
use App\Models\Archive\Document;

use Exception;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait CategoryTrait{
    use FileManagerTrait, LogTrait;

    private function archive_category_unique_id_create(){
        return config('app.short_code').'-'.dechex(time());
    }

    public function archive_category_create($data){
        DB::beginTransaction();

        try{
            $query = ArchiveCategory::create([
                'unique_id' => $this->archive_category_unique_id_create(),
                'name' => $data['name'],
                'parent_id' => $data['parent_id'] ?? null,
                'short' => $data['short'],
                'description' => $data['description'],
                'location' => $data['location'],
                'status' => $data['status'] ?? 1,
                'created_by' => auth('api')->id(),
                'updated_by' => auth('api')->id()
            ]);
            DB::commit();
            $this->log_user_activity('Archive Category created', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Archive Category created', null, false);
            return new Exception($e->getMessage());
        }
    }
    
    public function archive_category_delete($id){
        DB::beginTransaction();

        try{
            $query = ArchiveCategory::where('id', '=', $id)->first();
            $query->status = 0;
            $query->deleted_by = auth('api')->id();
            $query->deleted_at = date('Y-m-d H:i:s');

            $query->save();
            DB::commit();
            $this->log_user_activity('Archive Category deleted', $id, true);
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Archive Category created', null, false);
            return $e->getMessage();
        }
    }
    
    public function archive_category_get_all($type, $specific, $detailed, $paginated, $page){
        switch ($type){
            case 'active':
                $query = ArchiveCategory::where('status', '=', 1);
            break;
            case 'all':
                $query = ArchiveCategory::withTrashed();
            break;
            case 'inactive':
                $query = ArchiveCategory::withTrashed()->where('status', '=', 0);
            break;
        }
        $query = $query->orderBy('name', 'ASC');
        $query = $detailed ? $query->with(['documents', 'primary']) : $query;
        $query = $paginated ? $query->paginate($page) : $query->get();
        return $query;
    }

    public function archive_category_get_by($type, $id, $detailed){
        switch($type){
            case 'id':
                $query = ArchiveCategory::where('id', '=', $id);
            break;
            case 'unique_id':
                $query = ArchiveCategory::where('unique_id', '=', $id);
            break;
        }
        $query = $detailed ? $query->with(['documents', 'primary']) : $query;
        return $query;
    }

    public function archive_category_update($data, $id){
        DB::beginTransaction();

        try{
            $query = ArchiveCategory::find($id);
            
            $query->name = $data['name'];
            $query->parent_id = $data['parent_id'] ?? null;
            $query->short = $data['short'];
            $query->description = $data['description'];
            $query->location = $data['location'];
            $query->status = $data['status'] ?? 1;
            $query->updated_by = auth('api')->id();
            
            $query->save();

            DB::commit();
            $this->log_user_activity('Archive Category updated', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Archive Category updated', $id, false);
            return $e->getMessage();
        }
    }
}