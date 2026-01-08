<?php

namespace App\Models\EMR\Consultation;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Structure
{
    use HasFactory;

    protected $table = 'emr_consultation_services';

    protected $fillable = ['item_id', 'specialty_id', 'consultant_id', 'is_default', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at', 'deleted_by', 'deleted_at']; 

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function consultant()
    {
        return $this->belongsTo('App\Models\User', 'consultant_id', 'id');
    }

    public function specialty()
    {
        return $this->belongsTo('App\Models\EMR\Consultation\Specialty', 'specialty_id', 'id');
    }

    public function item()
    {
        return $this->belongsTo('App\Models\Inventory\Item', 'item_id', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope default consultation services
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Resolve the default service ID for a specialty
     */
    public static function resolveItemId(int $specialtyId, ?int $consultantId = null): ?int {
        // 1. Consultant-specific override
        if ($consultantId) {
            $itemId = static::where('specialty_id', $specialtyId)->where('consultant_id', $consultantId)->value('item_id');

            if ($itemId) {return $itemId;}
        }

        // 2. Specialty default
        return static::where('specialty_id', $specialtyId)->whereNull('consultant_id')->where('is_default', true)->value('item_id');
    }
}
