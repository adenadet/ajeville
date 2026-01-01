<?php

namespace App\Http\Controllers\Api\EMR\Laboratory;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\LaboratoryTrait;
use Illuminate\Http\Request;

use App\Models\EMR\LaboratoryRequest;
use App\Models\EMR\Visit;
use App\Models\Finance\Transaction;
use App\Models\Inventory\Item;


class RequestController extends Controller
{
    use LaboratoryTrait;

    public function collect($id)
    {
        $request = LaboratoryRequest::where('id', '=', $id)->first();

        $count = LaboratoryRequest::whereDate('date', '>=', date('Y').'-01-01')->withTrashed()->count();
        if (is_null ($request->unique_id)){
            $request->unique_id = config('app.short_code').'-'.sprintf("%06d", ++$count) ;
            $request->save();
        }

        return response()->json([
            'request' => LaboratoryRequest::where('id', '=', $id)->with(['patient.user', 'branch', 'creator', 'item', 'reporter', 'secondary_reporter', 'approver', 'collector', 'visit.patient'])->first(),
            'visit' =>  Visit::where('id', '=', $request->visit_id)->with('patient.user', 'visit_type', )->first(),
        ]);
    }

    public function dashboard()
    {
        return response()->json([
            'completed' => $this->laboratory_request_get_all('paid', null, false, false, $_GET['page'] ?? 1),
            'completed_referred_in' => $this->laboratory_request_get_all('referred_in', 'pending', false, false, $_GET['page'] ?? 1),
            'completed_referred_out' => $this->laboratory_request_get_all('referred_out', 'pending', false, false, $_GET['page'] ?? 1),
            'emergency' => $this->laboratory_request_get_all('emergency', null, false, false, $_GET['page'] ?? 1),
            'pending_new' => $this->laboratory_request_get_all('paid', 'today', false, false, $_GET['page'] ?? 1),
            'pending' => $this->laboratory_request_get_all('paid', null, false, false, $_GET['page'] ?? 1),
            'pending_referred_in' => $this->laboratory_request_get_all('referred_in', 'pending', false, false, $_GET['page'] ?? 1),
            'pending_referred_out' => $this->laboratory_request_get_all('referred_out', 'pending', false, false, $_GET['page'] ?? 1),
            'unapproved' => $this->laboratory_request_get_all('unapproved', null, false, false, $_GET['page'] ?? 1),
        ]);
    }
    
    public function index()
    {
        return response()->json([
            'requests' => $this->laboratory_pending_requests(),
        ]);
    }

    public function initials()
    {
        return response()->json([
            'services' => $this->laboratory_services_get_all(null, null, false, false, $_GET['page']),
        ]);
    }

    public function insurance()
    {
        $active_request = LaboratoryRequest::where('status', '=', 0)->pluck('transaction_id');
        // Get Insurable Transactions that are not in the active request list and have no related Lab Request yet
        $transactions = Transaction::whereIn('id', $active_request)->whereIn('paid_by', [1, 3])->pluck('id');
        
        return response()->json([
            'requests' => LaboratoryRequest::whereIn('transaction_id', $transactions)->with(['patient.user', 'branch', 'creator', 'item', 'reporter', 'secondary_reporter', 'approver', 'collector'])->latest()->paginate(30),
        ]);
    }

    public function referred_in()
    {
        return response()->json([
            'requests' => $this->laboratory_referred_in(),
        ]);
    }


    public function referred_out()
    {
        return response()->json([
            'requests' => $this->laboratory_referred_out(),
        ]);
    }

    public function store(Request $request)
    {
        foreach ($request->input('investigations') as $investigation){
            $request = $this->laboratory_request_create_request($investigation['date'] ?? date('Y-m-d H:i:s'), $request['visit_id'], $request['consultation_id'] ?? NULL, $request['branch_id'], $request['patient_id'], $investigation['id'], $investigation['description']);
        }

        return response()->json([
            'pending_requests' => $this->laboratory_request_get_all('pending', null, true, true, $_GET['page']??1),
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'request' => LaboratoryRequest::where('id', '=', $id)->with([ 'approver', 'collector', 'patient.user', 'branch', 'creator', 'item', 'reporter', 'requester.user', 'secondary_reporter', 'transaction.payments'])->first(),
        ]);
    }

    public function update(Request $request, $id)
    {
        //
    }
    
    public function destroy($id)
    {
        //
    }
}
