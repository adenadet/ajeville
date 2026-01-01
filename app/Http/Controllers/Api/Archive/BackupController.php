<?php

namespace App\Http\Controllers\Api\Archive;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BackupController extends Controller
{
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
        $category = $this->archive_category_create($request);
        
        return response()->json([
            'category' => $category,
        ], is_string($category) ? 500 : 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = $this->archive_category_get_by('unique_id', $id);
        
        return response()->json([
            'category' => $category,
        ], is_string($category) ? 500 : 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
