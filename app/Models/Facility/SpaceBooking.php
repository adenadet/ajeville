<?php

namespace App\Models\Facility;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpaceBooking extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'facility_space_bookings';
    protected $fillable = [
        'space_id', 'user_id', 'purpose', 'attendees', 'start_time', 'end_time', 'status', 'recurring', 'recurring_pattern','notes'
    ];

    protected $casts = [
        'start_time' => 'datetime', 'end_time' => 'datetime', 'recurring' => 'boolean'
    ];

    public function space()
    {
        return $this->belongsTo(Space::class);
    }

    public function user()
    {
        return $this->belongsTo('App/Models/User');
    }
}
