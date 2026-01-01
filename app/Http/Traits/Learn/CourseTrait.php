<?php
namespace App\Http\Traits\Learn;

use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\Learn\Category;
use App\Models\Learn\Course;
use App\Models\Learn\Exam;
use App\Models\Learn\Lesson;
use App\Models\Learn\Result;
use App\Models\Learn\UserCourse;
use App\Models\Learn\UserExam;
use App\Models\Learn\UserLesson;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait CourseTrait{
    use FileManagerTrait, LogTrait;
    public function learn_course_create($data){
        DB::beginTransaction();

        try{
            $query = Course::create([
                'name' => $data['name'],
                'category_id' => $data['category_id'],
                'sub_category_id' => $data['sub_category_id'],
                'price' => $data['price'],
                'exam' => $data['exam'],
                'exam_type_id' => $data['exam_type_id'],
                'certificate_type_id' => $data['certificate_type_id'],
                'description' => $data['description'],
                'status' => $data['status'] ?? 1,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);
            
            DB::commit();
            $this->log_user_activity('Learn Course create', $query->id, true); 
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Learn Course create', null, false);
            return $e->getMessage();
        }
    }

    public function learn_course_deactivate($id){
        DB::beginTransaction();

        try{    
            $query = Course::find($id);

            $query->status = $query->status == 1 ? 0 : 1;
            $query->updated_by = Auth::id() ?? auth('api')->id();

            $query->save();

            DB::commit();
            $this->log_user_activity('Learn Course deactivate', $id, true); 
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Learn Course deactivate', $id, false);
            return $e->getMessage();
        }

    }

    public function learn_course_delete($id){
        DB::beginTransaction();

        try{    
            $query = Course::find($id);

            $query->status = 0;
            $query->deleted_by = Auth::id() ?? auth('api')->id();
            $query->deleted_at = date('Y-m-d H:i:s');
            $query->save();

            DB::commit();
            $this->log_user_activity('Learn Course delete', $id, true); 
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Learn Course delete', $id, false);
            return $e->getMessage();
        }

    }

    public function learn_course_get_all($type, $specific, $detailed, $paginated, $page){
        $query = Course::query();

        switch($type){
            case 'active':
                $query = $query->where('status', '=', 1);
            break;
            case 'inactive':
                $query = $query->where('status', '=', 0);
            break; 
        }

        $query = $detailed ? $query->with(['creator', 'deleter', 'tutors']) : $query->select('id', 'name');
        $query = $query->orderBy('name', 'ASC');
        $query = $paginated ? $query->paginate(30) : $query->get();

        return $query;
    }

    public function learn_course_get_by($type, $id, $detailed){
        $query = Course::where('id', '=', $id);

        $query = $detailed ? $query->with(['assignees.user', 'category', 'creator', 'deleter', 'lessons', 'sub_category', 'tutors.user', ]) : $query->select('id', 'name');

        return $query->first();
    }

    public function learn_course_update($data, $id){
        DB::beginTransaction();

        try{
            $query = Course::find($id);

            $query->name = $data['name'] ?? $query->name;
            $query->category_id = $data['category_id'] ?? $query->category_id;
            $query->sub_category_id = $data['sub_category_id'] ?? $query->sub_category_id;
            $query->price = $data['price'] ?? $query->price;
            $query->exam = $data['exam'] ?? $query->exam;
            $query->exam_type_id = $data['exam_type_id'] ?? $query->exam_type_id;
            $query->certificate_type_id = $data['certificate_type_id'] ?? $query->certificate_type_id;
            $query->description = $data['description'] ?? $query->description;
            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->save();

            DB::commit();
            $this->log_user_activity('Learn Course update', $id, true); 
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Learn Course update', $id, false);
            return $e->getMessage();
        }
    }

    public function learn_course_category_create($data){}

    public function learn_course_category_deactivate($id){}

    public function learn_course_category_get_all($type, $specific, $detailed, $paginated, $page){}

    public function learn_course_category_get_by($type, $id, $detailed){}

    public function learn_course_category_update($data, $id){
        DB::beginTransaction();

        try{
            $query = Category::find($id);

            $query->name = $data['name'] ?? $query->name;
            $query->category_id = $data['category_id'] ?? $query->category_id;
            $query->sub_category_id = $data['sub_category_id'] ?? $query->sub_category_id;
            $query->price = $data['price'] ?? $query->price;
            $query->exam = $data['exam'] ?? $query->exam;
            $query->exam_type_id = $data['exam_type_id'] ?? $query->exam_type_id;
            $query->certificate_type_id = $data['certificate_type_id'] ?? $query->certificate_type_id;
            $query->description = $data['description'] ?? $query->description;
            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->save();

            DB::commit();
            $this->log_user_activity('Learn Course update', $id, true); 
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Learn Course update', $id, false);
            return $e->getMessage();
        }
    }

    public function learn_course_lesson_create($data){
        DB::beginTransaction();

        try{
            $file = $this->file_upload($data['file'], $data['file_type'], 'uploads/lessons', null);
            $query = Lesson::create([
                'name' => $data['name'], 
                'course_id' => $data['course_id'], 
                'content' => $data['content'] , 
                'lesson_type_id' => $data['type_id'],
                'file_type' => $data['file_type'] ?? null,
                'file' => $file ?? null,
                'video' => $data['video'] ?? null,
                'serial_number' => $data['serial_number'],
                'created_by' => auth('api')->id(),
                'updated_by' => auth('api')->id(),
            ]);

            DB::commit();
            $this->log_user_activity('Learn Lesson create', $query->id, true); 
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Learn Lesson create', null, false);
            return $e->getMessage();
        }
    }

    public function learn_course_lesson_deactivate($id){
        DB::beginTransaction();

        try{
            $query = Lesson::findOrFail($id);

            if($query->status == 1){
                $query->status = 0;
                $query->deleted_by = Auth::id() ?? auth('api')->id();
                $query->deleted_at = date('Y-m-d H:i:s');
            }
            else{
                $query->status = 1;
                $query->deleted_by = null;
                $query->deleted_at = null;
            }

            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->save();
            
            DB::commit();
            $this->log_user_activity('Learn Lesson deactivate', $id, true); 
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Learn Lesson deactivate', $id, false);
            return $e->getMessage();
        }  
    }

    public function learn_course_lesson_get_all($type, $specific, $search, $detailed, $paginated, $page){
        $query = Lesson::query();

        switch($type){
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'active':
                $query = $query->where('status', '=', 1);
            break;
            case 'course':
                $query = $query->where('course_id', '=', $specific);
            break;
            case 'inactive':
                $query = $query->where('status', '=', 0);
            break;     
        }

        $query = $detailed ? $query->with(['creator', 'updater', 'course']) : $query->select('id', 'name', 'course_id');
        $query = $query->orderBy('serial_number', 'ASC');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function learn_course_lesson_get_by($type, $id, $detailed){
        try{
            $query = Lesson::findOrFail($id);

            $query = $detailed ? $query->with(['creator', 'updater', 'course']) : $query->select('id', 'name', 'course_id');

            return $query->first();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function learn_course_lesson_update($data, $id){
        DB::beginTransaction();

        try{
            $file = $this->file_upload($data['file'], $data['file_type'], 'uploads/lessons', null);
            $query = Lesson::findOrFail($id);

            $query->name = $data['name']?? $query->name;
            $query->course_id = $data['course_id'] ?? $query->course_id;
            $query->content = $data['content'] ?? $query->content;
            $query->lesson_type_id = $data['lesson_type_id'] ?? $query->lesson_type_id;
            $query->file_type = $data['file_type'] ?? $query->file_type;
            $query->file = $file ?? $query->file;
            $query->video = $data['video'] ?? $query->video;
            $query->serial_number = $data['serial_number'] ?? $query->serial_number;
            $query->updated_by = auth('api')->id() ?? Auth::id();
            
            $query->save();

            DB::commit();
            $this->log_user_activity('Learn Lesson update', $id, true); 
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Learn Lesson update', $id, false);
            return $e->getMessage();
        }
    }

    public function learn_course_sub_category_create($data){}

    public function learn_course_sub_category_deactivate($id){}

    public function learn_course_sub_category_get_all($type, $specific, $detailed, $paginated, $page){}

    public function learn_course_sub_category_get_by($type, $id, $detailed){}

    public function learn_course_sub_category_update($data){}
}