<?php

namespace App\Http\Controllers\Api\Archive;

use App\Http\Controllers\Controller;
use App\Http\Traits\Archive\CategoryTrait;
use App\Http\Traits\Archive\DocumentTrait;
use App\Http\Traits\EMR\PatientTrait;
use App\Http\Traits\Ums\UserTrait;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use CategoryTrait, DocumentTrait, PatientTrait, UserTrait;

    public function index()
    {
        return response()->json([
            'categories' => $this->archive_category_get_all('active', null, true, false, null)->count(),
            'documents' => $this->archive_document_get_all('by-category', null, true, false, null),
            'patients' => $this->emr_patient_get_all('all', null, false, false, null)->count(),
            //'users' => $this->ums_user_get_all('all', null, false, false, null)->count(),
        ]);
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
