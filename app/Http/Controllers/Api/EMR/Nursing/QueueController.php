<?php

namespace App\Http\Controllers\Api\EMR\Nursing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Traits\QueueTrait;

class QueueController extends Controller
{
    use QueueTrait;

    public function destroy($id)
    {
        //
    }

    public function index()
    {
        return response()->json([
            'queues' => $this->nurses_queue(),
        ]);
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

}
