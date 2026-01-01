<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\Notice\NoticeTrait;
use App\Models\Notice;
use App\Models\NoticePicture;

class NoticeController extends Controller
{
    use NoticeTrait;
    public function index()
    {
        return response()->json([           
            'notices' => $this->notices_get_all($_GET['t'] ?? 'active', null, true, true, $_GET['page'] ?? 1),
        ]);
    }

    public function modify(Request $request)
    {
        $data = json_decode($request->data);

        $upload_path = "upload/notices";
        if((is_null($request->file)) || ($request->file == "")){$file_type = null; $fileName = null;}
        
        else{$fileName = time().'.'.$request->file->getClientOriginalExtension(); $request->file->move(public_path($upload_path), $fileName);}

        $notice = Notice::find($data->id);
        $notice->user_id = auth('api')->id();
        $notice->content =  $data->content;
        $notice->topic =  $data->topic;
        $notice->start_date = $data->start_date;
        $notice->end_date = $data->end_date;
        $notice->image = $fileName;

        $notice->save();
        

        return response()->json([
            'notices' => Notice::where('department_id', '=', 0)->orWhere('department_id', '=', auth('api')->user()->department_id)->andWhere('start_date', '>=', date('Y-m-d'))->orderBy('created_at', 'DESC')->get(),
        ]);
    }

    public function store(Request $request)
    {
        return response()->json([
            'notice' => $this->notices_create($request),
            'notices' => $this->notices_get_all('all', null, true, true, $_GET['page'] ?? 1),
        ]);
    
    }

    public function show($id)
    {
        return response()->json([
            'notice' => Notice::where('id', '=', $id)->with('creator')->first(),
        ]);
    }

    public function update(Request $request, $id)
    {
        return response()->json([
            'notice' => $this->notices_update($request, $id),
            'notices' => $this->notices_get_all('all', null, true, true, $_GET['page'] ?? 1),
        ]);
    }

    public function destroy($id)
    {
        $this->notices_delete($id);

        return response()->json(['notices' => $this->notices_get_all('all', null, true, true, $_GET['page'] ?? 1),]);

    }
}
