<?php

namespace App\Http\Controllers\Api\Facility;

use App\Http\Controllers\Controller;
use App\Http\Traits\Facility\SpaceTrait;
use Illuminate\Http\Request;

class SpaceController extends Controller
{
    use SpaceTrait;

    public function destroy(string $id)
    {
        $space = $this->facility_space_deactivate($id);

        return response()->json([
            'space' => $space,
        ], is_string($space)? 500 : 201);
    }

    public function index()
    {
        return response()->json([
            'spaces' => $this->facility_space_get_all($_GET['type'] ?? 'active', $_GET['query'] ?? null, true, true, $_GET['page'] ?? 1),
        ]);
    }

    public function store(Request $request)
    {
        $space = $this->facility_space_create($request);

        return response()->json([
            'space' => $space,
        ], is_string($space)? 500 : 201);
    }

    public function show(string $id)
    {
        $space = $this->facility_space_get_by(null, $id, true);
     
        return response()->json([
            'space' => $space,
        ], is_string($space)? 500 : 200);
    }

    public function update(Request $request, string $id)
    {
        $space = $this->facility_space_update($request, $id);
     
        return response()->json([
            'space' => $space,
        ], is_string($space)? 500 : 200);
    }
}
