<?php

namespace App\Http\Controllers\Api\EMR\Hims;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\ConsultantTrait;
use App\Http\Traits\EMR\PatientTrait;
use App\Http\Traits\EMR\VisitTrait;
use App\Http\Traits\Operations\BranchTrait;
use App\Http\Traits\Operations\ServiceTypeTrait;
use App\Models\EMR\Appointment\Appointment;
use App\Models\EMR\Consultation\SpecialtyDoctor;
use App\Models\EMR\Visit;
use App\Models\EMR\VisitType;
use App\Models\User;
use App\Models\EMR\Consultation\Schedule as ConsultantSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    use ConsultantTrait, BranchTrait, PatientTrait, ServiceTypeTrait, VisitTrait;

    public function available_slots(Request $request)
    {
        $consultantId = $request->consultant_id;
        $branchId = $request->branch_id;
        $date = $request->date;

        $dayOfWeek = Carbon::parse($date)->format('l'); // Monday, Tuesday, etc.

        // Step 1: Get consultant schedule for that day at branch
        $schedule = ConsultantSchedule::where('consultant_id', $consultantId)
            ->where('branch_id', $branchId)
            ->where('day_of_week', strtolower($dayOfWeek))
            ->first();

        if (!$schedule) return [];

        $slotDuration = $schedule->slot_duration; // in minutes
        $startTime = Carbon::parse($schedule->start_time);
        $endTime = Carbon::parse($schedule->end_time);

        // Step 2: Generate all slots
        $slots = [];
        $current = $startTime->copy();
        while ($current->lt($endTime)) {
            $slotEnd = $current->copy()->addMinutes($slotDuration);
            $slots[] = [
                'start' => $current->format('H:i'),
                'end' => $slotEnd->format('H:i'),
                'booked' => false
            ];
            $current->addMinutes($slotDuration);
        }

        // Step 3: Get existing appointments
        $bookedAppointments = Appointment::where('consultant_id', $consultantId)
            ->where('branch_id', $branchId)
            ->where('date', $date)
            ->pluck('time_slot')
            ->toArray();

        // Step 4: Mark booked slots
        foreach ($slots as &$slot) {
            if (in_array($slot['start'], $bookedAppointments)) {
                $slot['booked'] = true;
            }
        }

        return $slots;
    }

    public function check_in(Request $request)
    {
        $request->validate([
            'appointment_id' => ['numeric', 'required', 'exists:emr_appointments,id'], 
            'patient_id' => ['numeric', 'required', 'exists:emr_patients,id'],
            'consultant_id' => ['nullable', 'exists:users,id'],
            'service_type_id' => ['required', 'exists:emr_settings_service_types,id'],
            'specialty_id' => ['nullable', 'exists:emr_specialties,id'],
        ]);

        $visit = $this->emr_appointment_convert_to_visit($request, $request->appintment_id);

        return response()->json([
            'visit' => $visit,
        ], is_string($visit) ? 500 : 200);
    }
    public function index()
    {
        $appointments = $this->emr_appointment_get_all($_GET['status'] ?? 'pending', $_GET, true, true, $_GET['page'] ?? null);
        
        return response()->json([
            'appointments' => $appointments,
            'service_types' => $this-> operation_service_type_get_all('queueable', null, false, false),
            'specialties' => $this->emr_specialty_get_all('active', null, true, false),
        ], is_string($appointments) ? 500 : 200);
    }

    public function initials()
    {
        $doctors = SpecialtyDoctor::pluck('doctor_id');
        $consultants = User::select('id', 'first_name', 'last_name')->whereIn('id', $doctors)->get(); 
        return response()->json([
            'branches' => $this->operation_branch_get_all(false, false, null),
            'consultants' => $consultants,
            'patients' => $this->emr_patient_get_all('active', null, false, false, null),
            'service_types' => $this-> operation_service_type_get_all('queueable', null, false, false),
            'specialties' => $this->emr_specialty_get_all('active', null, true, false),
        ]);
    }

    public function show(String $id)
    {
        $appointment = $this->emr_appointment_get_by('id', $id,  true);

        return response()->json(['appointment' => $appointment],is_string($appointment) ? 500 : 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => ['numeric', 'nullable', 'exists:emr_patients,id'],
            'consultant_id' => ['nullable', 'exists:users,id'],
            'service_type_id' => ['required', 'exists:emr_settings_service_types,id'],
            'specialty_id' => ['nullable', 'exists:emr_specialties,id'],
            'date' => ['required', 'date', 'after_or_equal:now'],
            'time_slot' => ['required'],
            'remarks' => ['nullable', 'string']
        ]);

        $appointment = $this->emr_appointment_create($request);

        return response()->json(
            [
                'appointment' => $appointment,
            ], 
        is_string($appointment) ? 500 : 201);
    }
    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'doctor_id'  => ['sometimes', 'exists:users,id'],
            'consultation_group_id' => ['sometimes', 'nullable', 'exists:consultation_groups,id'],
            'appointment_at' => ['sometimes', 'date', 'after_or_equal:now'],
            'status' => ['sometimes', 'in:scheduled,cancelled,completed'],
        ]);

        $appointment->update($validated);

        return response()->json($appointment->fresh(['patient','doctor']));
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return response()->noContent();
    }

    public function search(Request $request)
    {
        $request->validate([
            'doctor_id' => ['sometimes', 'exists:users,id'],
            'consultation_group_id' => ['sometimes', 'exists:consultation_groups,id'],
            'patient_name' => ['sometimes', 'string'],
            'date' => ['sometimes', 'date'],
        ]);

        $query = Appointment::with(['patient', 'doctor']);

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->filled('consultation_group_id')) {
            $query->where('consultation_group_id', $request->consultation_group_id);
        }

        if ($request->filled('patient_name')) {
            $query->whereHas('patient', function ($q) use ($request) {
                $q->where('first_name', 'like', '%'.$request->patient_name.'%')
                  ->orWhere('last_name', 'like', '%'.$request->patient_name.'%');
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('appointment_at', $request->date);
        }

        return $query->orderBy('appointment_at')->paginate(20);
    }

    /**
     * Convert an appointment to a visit.
     */
    public function convertToVisit(Request $request, Appointment $appointment)
    {
        if ($appointment->visit) {
            return response()->json(['message' => 'Visit already exists for this appointment.'], 409);
        }

        $visit = DB::transaction(function () use ($appointment, $request) {
            $visit = Visit::create([
                'appointment_id' => $appointment->id,
                'patient_id'     => $appointment->patient_id,
                'doctor_id'      => $appointment->doctor_id,
                'started_at'     => now(),
                'notes'          => $request->input('notes'),
            ]);

            $appointment->update(['status' => 'completed']);

            return $visit;
        });

        return response()->json($visit->load(['patient','doctor']), 201);
    }
}
