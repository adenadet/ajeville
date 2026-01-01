<?php
namespace App\Http\Traits\Notice;
use App\Http\Traits\General\FileManagerTrait;
use App\Models\Notice;
use App\Models\NoticePicture;
use Illuminate\Support\Facades\Auth;

trait NoticeTrait{
    use FileManagerTrait;
    public function notices_create($data){
        $notice = Notice::create([
            'image' => $this->file_upload($data['file'], $data['file_type'], 'uploads/notices', null),
            'user_id' => (Auth::id() ?? auth('api')->id()),
            'content' =>  $data['content'],
            'topic' =>  $data['topic'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
        ]);

        return $notice;
    }

    public function notices_delete($id){
        $notice = Notice::find($id);

        $notice->deleted_by = (Auth::id() ?? auth('api')->id());
        $notice->deleted_at = date('Y-m-d H:i:s');
        
        $notice->save();

        return $notice;
    }

    public function notices_get_all($type, $specific, $detailed, $paginated, $page){
        switch ($type){
            case 'all':
                $query = Notice::with('creator');
            break;
            case 'active':
                $query = Notice::where('start_date', '>=', date('Y-m-d'))->where('end_date', '<=', date('Y-m-d'))->with('creator');
            break;
            case 'dashboard':
                $query = Notice::with('creator')->limit(5)->latest();
            break;
        }
        $query = $detailed ? $query->with(['creator'])->latest() : $query->latest();
        $query = $paginated ? $query->paginate(20) : $query->get();
        return $query;
    }

    public function notices_get_by_id($id){
        return Notice::where('id', '=', $id)->with('creator')->first();
    }
    public function notices_update($data, $id){
        $fileName = $this->file_upload($data['file'], 'image', '/uploads/notices', $id);

        $notice = Notice::find($id);

        $notice->image = $fileName;
        $notice->content =  $data['content'];
        $notice->topic =  $data['topic'];
        $notice->start_date = $data['start_date'];
        $notice->end_date = $data['end_date'];
        
        $notice->save();

        return $notice;
    }

}