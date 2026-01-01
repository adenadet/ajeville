<?php

namespace App\Http\Controllers\Api\Hrms;

use App\Http\Controllers\Controller;
use App\Http\Traits\Hrms\AttendanceTrait;
use App\Models\Hrms\Employee;
use Illuminate\Http\Request;

class ClockInController extends Controller
{
    use AttendanceTrait;

    public function destroy(string $id)
    {
        $clock_in = $this->hrms_attendance_clock_in_delete($id);

        return response()->json(['clock_in' => $clock_in], is_string($clock_in) ? 404 : 200);
    }
    
    public function index()
    {
        $clock_ins = $this->hrms_attendance_clock_in_get_all($_GET['type'] ?? 'my_month', $_GET['specific'] ?? null, true, true, $_GET['query'] ?? null);
    
        return response()->json(['clock_ins' => $clock_ins], is_string($clock_ins) ? 400 : 200);
    }

    public function show(string $id)
    {
        //
    }

    public function store(Request $request)
    {
        $employee_id = Employee::where('user_id', auth('api')->id())->value('id');

        $data = [
            'employee_id' => $employee_id,
            'clock_in_time' => now(),
            'source' => $request->input('source'),
        ];

        $clock_in = $this->hrms_attendance_clock_in_create($data);

        return response()->json(['clock_in' => $clock_in], is_string($clock_in) ? 400 : 201);
    }

    public function update(Request $request, string $id)
    {
        $clock_in = $this->hrms_attendance_clock_in_update( $request->all(),$id);

        return response()->json(['clock_in' => $clock_in], is_string($clock_in) ? 400 : 200);
    }

}
