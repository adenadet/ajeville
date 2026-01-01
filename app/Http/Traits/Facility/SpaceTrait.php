<?php

namespace App\Http\Traits\Facility;

use App\Http\Traits\General\LogTrait;
use App\Models\Facility\Space;
use App\Models\Facility\SpaceType;
use App\Models\Operations\Branch;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait SpaceTrait{


    /*
    -----------------------------------------------------------------
    Basic Space Functions
    -----------------------------------------------------------------
    */
    public function facility_space_create($data){
        DB::beginTransaction();
        try{
            $space = Space::create([
                'name' => $data['name'],
                'code' => $data['code'] ?? null,
                'uuid' => Str::uuid()->toString(),
                'type_id' => $data['type_id'],
                'department_id' => $data['department_id'] ?? null,
                'floor_id' => $data['floor_id'] ?? null,
                'building_id' => $data['building_id'] ?? 1,
                'area_id' => $data['area_id'],
                'capacity' => $data['capacity'] ?? null,
                'status' => $data['status'] ?? Space::StatusActive,
                'description' => $data['description'] ?? null,
                'is_available' => $data['is_available'] ?? 1,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);
            DB::commit();
            return $space;
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function facility_space_deactivate($id){
        DB::beginTransaction();
        try{
            $space = Space::where('id','=', $id)->orWhere('unique_id', '=', $id)->orWhere('code', '=', $id)->first();

            $space->status = $space->status == Space::StatusActive ? Space::StatusInactive : Space::StatusActive;
            $space->updated_by = Auth::id() ?? auth('api')->id();
            $space->save();

            DB::commit();
            return $space;
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function facility_space_get_all($type, $specific, $detailed, $paginated, $page){
        $query = Space::query();
        switch($type){
            case 'all':
            case 'active':
                $query = $query->where('status', '=', Space::StatusActive);
                if (!(is_null($specific))){
                    $query = $query->where('name', 'LIKE', "%$specific%");
                }
            break;
            case 'building':
                $query = $query->where('building_id', '=', $specific);
            case 'inactive':
                $query = $query->where('status', '=', Space::StatusInactive);
                if (!(is_null($specific))){
                    $query = $query->where('name', 'LIKE', "%$specific%");
                }
            break;
        }

        $query->orderBy('name', 'ASC');
        $query = $detailed  ? $query->with(['building', 'department', 'floor', 'type']) : $query->select('id', 'name', 'code');
        $query = $paginated ? $query->paginate(20) : $query->get();
        return $query;
    }

    
    public function facility_space_get_by($type, $id, $detailed){
        $query = Space::where('id','=', $id)->orWhere('unique_id', '=', $id)->orWhere('code', '=', $id)->first();

        $query = $detailed ? $query->with(['building', 'department', 'floor', 'type']) : $query->select('id', 'name', 'code');
        return $query;
    }

    public function facility_space_report($type, $specific){
        $query = Space::query();
        switch($type){
            case 'utilization':
                $query = $query->where('status', '=', Space::StatusActive);
                $totalSpaces = $query->count();
                $occupiedSpaces = $query->where('is_available', false)->count();
                $availableSpaces = $query->where('is_available', true)->count();
                
                $utilizationRate = $totalSpaces > 0 ? ($occupiedSpaces / $totalSpaces) * 100 : 0;

                return response()->json([
                    'report' => [
                        'total_spaces' => $totalSpaces,
                        'occupied_spaces' => $occupiedSpaces,
                        'available_spaces' => $availableSpaces,
                        'utilization_rate' => round($utilizationRate, 2)
                    ],
                ]);
        }
    
    }

    public function facility_space_update($data, $id){
        DB::beginTransaction();
        try{

            $query = Space::where('id','=', $id)->orWhere('unique_id', '=', $id)->orWhere('code', '=', $id)->first();

            $query->name = $data['name'];
            $query->code = $data['code'] ?? null;
            $query->uuid = Str::uuid()->toString();
            $query->type_id = $data['type_id'];
            $query->department_id = $data['department_id'] ?? null;
            $query->floor_id = $data['floor_id'] ?? null;
            $query->building_id = $data['building_id'] ?? 1;
            $query->area_id = $data['area_id'];
            $query->capacity = $data['capacity'] ?? null;
            $query->status = $data['status'] ?? Space::StatusActive;
            $query->description = $data['description'] ?? null;
            $query->is_available = $data['is_available'] ?? 1;
            $query->updated_by = auth('api')->id() ?? Auth::id();

            $query->save();
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }

    /*
    -----------------------------------------------------------------
    Basic Space Type Functions
    -----------------------------------------------------------------
    */

    public function facility_space_type_create($data){
        DB::beginTransaction();

        try{
            $query = SpaceType::create([
                'name' => $data['name'],
                'code' => Str::uuid()->toString(),    
                'description' => $data['description'] ?? null,
                'rate_per_hour' => $data['rate_per_hour'] ?? 0.00,
                'rate_per_day' => $data['rate_per_day'] ?? 0.00,
                'status' => $data['status'] ?? SpaceType::StatusActive,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function facility_space_type_deactivate($id){
        DB::beginTransaction();

        try{
            $query = SpaceType::where('id', '=', $id)->orWhere('uuid', '=', $id)->firstOrFail();
            
            $query->name = $data['name'] ?? $query->name;
            
            $query->description = $data['description'] ?? $query->description;
            $query->rate_per_hour = $data['rate_per_hour'] ?? $query->rate_per_hour;
            $query->rate_per_day = $data['rate_per_day'] ?? $query->rate_per_day;

            $query->save();

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function facility_space_type_get_all(){}

    public function facility_space_type_get_by(){}

    public function facility_space_type_update($data, $id){
        DB::beginTransaction();

        try{
            $query = SpaceType::where('id', '=', $id)->orWhere('uuid', '=', $id)->firstOrFail();
            
            $query->name = $data['name'] ?? $query->name;
            $query->description = $data['description'] ?? $query->description;
            $query->rate_per_hour = $data['rate_per_hour'] ?? $query->rate_per_hour;
            $query->rate_per_day = $data['rate_per_day'] ?? $query->rate_per_day;
            $query->updated_by = Auth::id() ?? auth('api')->id();

            $query->save();

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }
}