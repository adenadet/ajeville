<?php 

namespace App\Http\Traits\Hrms;

use App\Http\Traits\General\LogTrait;
use App\Http\Traits\Hrms\EmployeeTrait;
use App\Http\Traits\UMS\UserTrait;

use App\Models\Hrms\AttendanceSummary;
use App\Models\Hrms\Branch;
use App\Models\Hrms\ClockIn;
use App\Models\Hrms\Employee;
use App\Models\Hrms\EmployeeExperience;
use App\Models\Hrms\EmployeeLeaveType;
use App\Models\Hrms\EmployeeSalary;
use App\Models\Hrms\EmployeeTraining;
use App\Models\Hrms\Job;
use App\Models\Hrms\Leave;
use App\Models\Hrms\LeaveType;
use App\Models\Hrms\Shift;
use App\Models\Hrms\ShiftType;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mail;
use Session;

trait AttendanceTrait{
    use LogTrait;
    /*
    -------------------------------------------------------------------
    Clock In CRUD Functions
    -------------------------------------------------------------------
    */
    public function hrms_attendance_clock_in_create($data){
        DB::beginTransaction();

        try {
            $clockIn = ClockIn::create([
                'employee_id' => $data['employee_id'] ?? Employee::where('user_id', '=', auth('api')->id() ?? Auth::id())->first()->id(),
                'clock_in_time' =>  $data['clock_in_time']  ?? now(),
                'source' => $specific['source'] ?? 'web',
            ]);

            DB::commit();
            $this->log_user_activity('Hrms Clock In Creation', $clockIn->id, true);
            return $clockIn;
        } 
        catch (Exception $e) {
            DB::rollBack();
            $this->log_user_activity('Hrms Clock In Creation', null, false);
            return $e->getMessage();
        }
    }

    public function hrms_attendance_clock_in_delete($id){
        DB::beginTransaction();

        try {
            $clockIn = ClockIn::findOrFail($id);
            $clockIn::update(['deleted_at' => now()]);

            DB::commit();
            $this->log_user_activity('Hrms Clock In Delete', $id, true);
            return $clockIn;
        } 
        catch (Exception $e) {
            DB::rollBack();
            $this->log_user_activity('Hrms Clock In Delete', $id, false);
            return $e->getMessage();
        }
    }

    public function hrms_attendance_clock_in_get_all($type, $specific, $detailed, $paginated, $searchQuery){
        $query = ClockIn::query();
        switch($type){
            case 'admin_month':
                $query = $query->whereDate('clock_in_time', '>=', $specific.'-01')
                              ->whereDate('clock_in_time', '<=', Carbon::parse($specific)->endOfMonth());
            break;
            case 'mine':
                $query = $query->where('employee_id', auth('api')->id() ?? Auth::id());
                if (isset($specific['start_date']) && isset($specific['end_date'])) {
                    $query = $query->whereDate('clock_in_time', '>=', $specific['start_date'])
                                   ->whereDate('clock_in_time', '<=', $specific['end_date']);
                }
            break;
            case 'my_month':
                $query = $query->where('employee_id', auth('api')->id() ?? Auth::id())
                               ->whereDate('clock_in_time', '>=', now()->format('Y-m-01'))
                               ->whereDate('clock_in_time', '<=', Carbon::now()->endOfMonth());
            break;
            case 'staff':
                $query = $query->where('employee_id', $specific['staff_id']);
                if (isset($specific['start_date']) && isset($specific['end_date'])) {
                    $query = $query->whereDate('clock_in_time', '>=', $specific['start_date'])
                                   ->whereDate('clock_in_time', '<=', $specific['end_date']);
                }
            break;
            case 'staff_month':
                $query = $query->where('employee_id', $specific['staff_id'])
                               ->whereDate('clock_in_time', '>=', now()->format('Y-m-01'))
                               ->whereDate('clock_in_time', '<=', Carbon::now()->endOfMonth());
            break;
        }

        $query = $detailed ? $query->with(['employee']) : $query->select('id', 'employee_id', 'clock_in_time', 'source');
        $query->orderBy('clock_in_time', 'desc');
        $query = $paginated ? $query->paginate(10) : $query->get();

        return $query;
    }

    public function hrms_attendance_clock_in_update($data, $id){
        DB::beginTransaction();

        try {
            $clockIn = ClockIn::findOrFail($id);
            $clockIn::update([
                'employee_id' => $data['employee_id'] ?? auth('api')->id() ?? Auth::id(),
                'clock_in_time' =>  $data['clock_in_time']  ?? now(),
                'source' => $specific['source'] ?? 'web',
            ]);

            DB::commit();
            $this->log_user_activity('Hrms Clock In Update', $id, true);
            return $clockIn;
        } 
        catch (Exception $e) {
            DB::rollBack();
            $this->log_user_activity('Hrms Clock In Update', $id, false);
            return $e->getMessage();
        }
    }

    /*
    -------------------------------------------------------------------
    Attendnace Summary CRUD Functions
    -------------------------------------------------------------------
    */
    public function hrms_attendance_summary_create($data){
        DB::beginTransaction();
        $period = CarbonPeriod::create($data['start_date'], $data['end_date']);
        try {
            
                foreach ($period as $day) {
                    AttendanceSummary::updateOrCreate(
                        ['employee_id' => $data['employee_id'], 'date' => $day->toDateString()],
                        ['shift_id' => $data['shift_id']]
                    );
                }
            

            DB::commit();
            return 'Manual scheduling completed.';
        } 
        catch (Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }
    
    public function hrms_attendance_summary_create_bulk($data){
        $period = CarbonPeriod::create($data['start_date'], $data['end_date']);

        // Build query based on filters
        $query = Employee::query();

        if (isset($data['employee_ids']) && is_array($data['employee_ids']) && count($data['employee_ids']) > 0) {
            $query->whereIn('id', $data['employee_ids']);
        } 
        else {
            if (isset($data['department_id']) && $data['department_id'] !== null) {
                $query->where('department_id', $data['department_id']);
            }

            if (isset($data['team_id']) && $data['team_id'] !== null) {
                $query->where('team_id', $data['team_id']);
            }
        }

        $employees = $query->get();

        DB::beginTransaction();

        try {
            foreach ($employees as $employee) {
                foreach ($period as $day) {
                    AttendanceSummary::updateOrCreate(
                        ['employee_id' => $employee->id, 'date' => $day->toDateString()],
                        ['shift_id' => $data['shift_id'], ]
                    );
                }
            }

            DB::commit();
            return 'Manual scheduling completed.';
        } 
        catch (Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function hrms_attendance_summary_delete($id){
        /*
        $attendance = AttendanceSummary::where('id', '=', $id)->first();

        $attendance->deleted_at = date('Y-m-d H:i:s');
        $attendance->save();
        */
    }

    public function hrms_attendance_summary_get_all($type, $specific, $detailed, $paginated, $page){
        $query = AttendanceSummary::query();

        switch($type){
            case 'mine':
                $employee = Employee::where('user_id', '=', Auth::id() ?? auth('api')->id())->first();
                $query = $query->where('employee_id', '=', $employee->id);
            break;
        }

        if (is_array($specific)){
            $query = isset($specific['start_date']) ? $query->whereDate('date', '>=', $specific['start_date']) : $query;
        }
        $query = $detailed ? $query->with(['employee.user', 'shift']) : $query->select('id', 'date', 'shift_id', 'clock_in', 'clock_out');
        $query->orderBy('date', 'DESC');
        $query = $paginated ? $query->paginated(30) : $query->get();

        return $query;
    }

    /*
    -------------------------------------------------------------------
    Basic CRUD Operations for Employee Shift Types
    -------------------------------------------------------------------
    */
    public function hrms_employee_shift_type_create($data){

    }

    public function hrms_schedule_auto(){
        $month = $data['month'] ?? now()->formt('Y-m');

        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth = Carbon::parse($month)->endOfMonth();

        $employees = Employee::whereNotNull('shift_type_id')->with('shift_type')->get();
        $days = CarbonPeriod::create($startOfMonth, $endOfMonth);

        DB::beginTransaction();
        try {
            foreach ($employees as $employee) {
                $shiftType = $employee->shiftType;
                $daysOfWeek = $shiftType->days_of_week; // assume cast to array

                foreach ($days as $day) {
                    if (in_array($day->format('l'), $daysOfWeek)) {
                        // Check if matching Shift exists
                        $shift = Shift::firstOrCreate([
                            'name'       => $shiftType->name,
                            'start_time' => $shiftType->start_time,
                            'end_time'   => $shiftType->end_time,
                        ]);

                        // Create or update Employee Attendance Summary
                        AttendanceSummary::updateOrCreate(
                            ['employee_id' => $employee->id, 'date' => $day->toDateString()],
                            ['shift_id' => $shift->id]
                        );
                    }
                }
            }

            DB::commit();
            return 'Auto-scheduling completed.';
        } 
        catch (Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function hrms_schedule_manual($data){
        
    }

    /*
    -------------------------------------------------------------------
    Basic CRUD Operations for Shift
    -------------------------------------------------------------------
    */

    public function hrms_attendance_shift_create($data){
        DB::beginTransaction();

        try{
            $query = Shift::create([
                'name' => $data['name'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'overnight' => $data['end_time'] < $data['start_time'] ? 1 : 0, // Check if end time is before start time
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            DB::commit();
            $this->log_user_activity('Hrms Shift Creation', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Hrms Shift Creation', null, false);
            return $e->getMessage();
        }
    }

    public function hrms_attendance_shift_delete($id){
        DB::beginTransaction();

        try{
            $query = Shift::find($id);
                
            $query->deleted_by = auth('api')->id() ?? Auth::id();
            $query->deleted_at = now();
            $query->save();

            DB::commit();
            $this->log_user_activity('Hrms Shift Delete', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Hrms Shift Delete', $id, false);
            return $e->getMessage();
        }
    }

    public function hrms_attendance_shift_get_all( $paginated){
        return $query = $paginated ? Shift::orderBy('name', 'ASC')->paginated(50) : Shift::orderBy('name', 'ASC')->get();
    }

    public function hrms_attendance_shift_update($data, $id){
        DB::beginTransaction();

        try{
            $data['updated_by'] = auth('api')->id() ?? Auth::id();
            $query = Shift::where('id', $id)->update([
                'name' => $data['name'],
                'start_time' => $data['start_time'],
                'overnight' => $data['end_time'] < $data['start_time'] ? 1 : 0, // Check if end time is before start time
                'end_time' => $data['end_time'],
                'updated_by' => $data['updated_by'],
            ]);

            DB::commit();
            $this->log_user_activity('Hrms Shift Update', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Hrms Shift Update', $id, false);
            return $e->getMessage();
        }
    }

    /*
    -------------------------------------------------------------------
    Basic CRUD Operations for Shift Types
    -------------------------------------------------------------------
    */
    public function hrms_shift_type_create($data){
        DB::beginTransaction();

        try{
            $query = ShiftType::create([
                'name' => $data['name'],
                'shift_id' => $data['shift_id'],
                'days_of_week' => json_encode($data['days_of_week']),
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            DB::commit();
            $this->log_user_activity('Hrms Shift Type Creation', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Hrms Shift Type Creation', null, false);
            return $e->getMessage();
        }
    }

    public function hrms_shift_type_delete($id){
        DB::beginTransaction();

        try{

            $query = Shift::where('id', $id)->update([
                'deleted_by' => auth('api')->id() ?? Auth::id(),
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);
            DB::commit();
            $this->log_user_activity('Hrms Shift Type Delete', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Hrms Shift Type Delete', $id, false);
            return $e->getMessage();
        }
    }

    public function hrms_shift_type_get_all($detailed, $paginated){
        $query = ShiftType::query();

        $query = $detailed ? $query->with(['employees', 'shift']) : $query->select('id', 'name');
        $query->orderBy('name', 'asc');
        $query = $paginated ? $query->paginate(10) : $query->get();

        return $query;
    }

    public function hrms_shift_type_get_by($id, $detailed){
        $query = ShiftType::where('id', '=', $id);
        $query = $detailed ? $query->with(['employees', 'shift']) : $query->select('id', 'name');
        return $query->first();
    }

    public function hrms_shift_type_update($data, $id){
        DB::beginTransaction();

        try{
            $query = ShiftType::where('id', $id)->update([
                'name' => $data['name'],
                'shift_id' => $data['shift_id'],
                'days_of_week' => json_encode($data['days_of_week']),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            DB::commit();
            $this->log_user_activity('Hrms Shift Type Update', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Hrms Shift Type Update', $id, false);
            return $e->getMessage();
        }
    }
}