<?php

namespace App\Http\Controllers\Api\EMR\Radiology;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\RadiologyTrait;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    use RadiologyTrait;
    public function index()
    {
        $referrals = $this->emr_radiology_referral_get_all($_GET['type'], $_GET['status'], $_GET['search'], true, true, $_GET['page'] ?? 1);
        
        response()->json(['referrals' => $referrals]);
    }

    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
