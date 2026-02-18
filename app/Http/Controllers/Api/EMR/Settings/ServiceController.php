<?php

namespace App\Http\Controllers\Api\EMR\Settings;

use App\Http\Controllers\Controller;
use App\Models\EMR\Service;
use App\Models\EMR\Settings\Category;
use App\Models\EMR\Settings\ServiceType;
use Illuminate\Http\Request;

class ServiceController extends Controller
{

    public function destroy($id)
    {
        //
    }

    public function index()
    {
        //
    }

    public function initials()
    {
        return response()->json([
            'categories' => Category::orderBy('name', 'ASC')->get(),
            'service_types' => ServiceType::orderBy('name', 'ASC')->get(),
            'services' => Service::orderBy('name', 'ASC')->get(),        
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
}
