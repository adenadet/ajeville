<?php

namespace App\Http\Traits\Facility;

use App\Http\Traits\General\LogTrait;
use App\Models\Facility\Building;
use App\Models\Facility\BuildingImage;
use App\Models\Operations\Branch;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait BuildingTrait{

    /*
    -----------------------------------------------------------------------
    Basic Building Functions
    -----------------------------------------------------------------------
    */
    public function facility_building_create($data){
        DB::beginTransaction();
        try{
            $query = Building::create([
                'name' => $data['name'],
                'code' => $data['code'],
                'uuid' => Str::uuid()->toString(),
                'address' => $data['address'] ?? null,
                'location' => $data['location'] ?? null,
                'year_built' => $data['year_built'] ?? null,,
                'total_floors' => $data['total_floors'] ?? null,,
                'total_area' => $data['total_area'] ?? null,
                'owner' => $data['owner'] ?? null,
                'status' => $data['status'] ?? Building::StatusActive,
                'description' => $data['description'] ?? null,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function facility_building_deactivate($id){
        DB::beginTransaction();
        try{
            $query = Building::where('id', '=', $id)->orWhere('code', '=', $id)->orWhere('uuid', '=', $id)->first();
            
            $query->status =  $query->status == Building::StatusActive ? Building::StatusInactive : Building::StatusActive;
            $query->deleted_by = auth('api')->id() ?? Auth::id();
            
            $query->save();

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function facility_building_get_all($type, $specific, $detailed, $paginated, $page){
        $query = Building::query();

        switch($type){
            case 'active':
                $query = $query->where('id', '=', Building::StatusActive);
            break;
            case 'inactive':
                $query = $query->where('id', '=', Building::StatusInactive);
            break;
        }

        $query = $detailed ? $query->with([]) : $query->select('id', 'name');
        $query = $query->orderBy('name', 'ASC');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function facility_building_get_by($type, $id, $detailed){
        $query = Building::where('id', '=', $id)->orWhere('code', '=', $id)->orWhere('uuid', '=', $id)->first();
        
        $query = $detailed ? $query->with([]) : $query->select('id', 'name');

        return $query; 
    }

    public function facility_building_update($data, $id){
        DB::beginTransaction();
        try{
            $query = Building::where('id', '=', $id)->orWhere('code', '=', $id)->orWhere('uuid', '=', $id)->first();
            
            $query->name = $data['name'];
            $query->code = $data['code'];
            $query->uuid = Str::uuid()->toString();
            $query->address = $data['address'] ?? null;
            $query->location = $data['location'] ?? null;
            $query->year_built = $data['year_built'] ?? null;
            $query->total_floors = $data['total_floors'] ?? null;
            $query->total_area = $data['total_area'] ?? null;
            $query->owner = $data['owner'] ?? null;
            $query->status = $data['status'] ?? Building::StatusActive;
            $query->description = $data['description'] ?? null;
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
    -----------------------------------------------------------------------
    Basic Building Images Functions
    -----------------------------------------------------------------------
    */
    public function facility_building_image_create($data, $id){
        DB::beginTransaction();
        try{
            $query = BuildingImage::create([
                'building_id' => $data['building_id'],
                'source' => $data['source'] ?? null,
                'description' => $data['description'] ?? null,
                'is_primary' => $data['is_primary'] ?? 0,
                'status' => $data['status'] ?? BuildingImage::StatusActive,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function facility_building_image_make_primary($id){
        DB::beginTransaction();
        try{
            $query = BuildingImage::where('id', '=', $id)->first();
            
            $query->is_primary = 1;
            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->save();
    
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function facility_building_image_deactivate($id){
        DB::beginTransaction();
        try{
            $query = BuildingImage::where('id', '=', $id)->first();
            
            if ($query->is_primary){
                $building_image = BuildingImage::where('building_id', '=', $query->building_id)->where('id', '!=', $id)->where('status', '=', BuildingImage::StatusInactive)->first();

                if ($building_image){
                    $building_image->is_primary = 1;
                    $building_image->updated_by = Auth::id() ?? auth('api')->id();
                    $building_image->save();
                }
            }

            $query->status = BuildingImage::StatusInactive;
            $query->updated_by = Auth::id() ?? auth('api')->id();  
            $query->save();

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }
}