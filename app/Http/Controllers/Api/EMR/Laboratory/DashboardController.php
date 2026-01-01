<?php

namespace App\Http\Controllers\Api\EMR\Laboratory;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\LaboratoryTrait;
use App\Http\Traits\General\LogTrait;
use App\Http\Traits\EMR\QueueTrait;
use App\Models\EMR\LaboratoryRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use LaboratoryTrait, LogTrait;

    public function index(){
        return response()->json([
            'completed' => $this->laboratory_request_get_all('paid', null, false, false, $_GET['page'] ?? 1),
            'completed_referred_in' => $this->laboratory_request_get_all('referred_in', 'pending', false, false, $_GET['page'] ?? 1),
            'completed_referred_out' => $this->laboratory_request_get_all('referred_out', 'pending', false, false, $_GET['page'] ?? 1),
            'emergency' => $this->laboratory_request_get_all('emergency', null, false, false, $_GET['page'] ?? 1),
            'new' => $this->laboratory_request_get_all('paid', null, false, false, $_GET['page'] ?? 1),
            'pending_referred_in' => $this->laboratory_request_get_all('referred_in', 'pending', false, false, $_GET['page'] ?? 1),
            'pending_referred_out' => $this->laboratory_request_get_all('referred_out', 'pending', false, false, $_GET['page'] ?? 1),
            'unapproved' => $this->laboratory_request_get_all('unapproved', null, false, false, $_GET['page'] ?? 1),
        ]);
    }
    

    public function show($id)
    {
        //
    }

    public function store(Request $request)
    {
        //
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
