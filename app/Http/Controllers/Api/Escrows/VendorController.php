<?php

namespace App\Http\Controllers\Api\Escrows;

use App\Http\Controllers\Controller;
use App\Http\Traits\Ums\CompanyTrait;
use App\Models\Ums\Company;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    use CompanyTrait;

    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        $query = $this->ums_company_create($request);

        return response()->json(['company' => $query], is_string($query) ? 500 : 201);
    }

    public function show(string $id)
    {
        $vendor = $this->ums_company_get_by('primary_key', $id, false);

        return response()->json(['vendor' => $vendor], is_string($vendor) ? 404: 200);
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
