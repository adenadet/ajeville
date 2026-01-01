<?php

namespace App\Http\Controllers\Api\Ums;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
class BirthdayController extends Controller
{
    public function latest(){
        return response()->json([
            'birthdays'     => User::birthDayBetween(Carbon::now(), Carbon::now()->addWeek())->limit(8)->get(),
        ]);
    }

    public function index()
    {
        return response()->json([
            'birthdays'     => User::birthDayBetween(Carbon::now(), Carbon::now()->addWeek())->limit(8)->get(),
        ]);
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
