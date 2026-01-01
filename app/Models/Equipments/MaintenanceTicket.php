<?php

namespace App\Models\Equipments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceTicket extends Model
{
    use HasFactory;

    const StatusOpen = 1;
    const StatusAssigned = 2;
    const StatusAccepted = 3;
    const StatusResolved = 4;
    const StatusClosed = 5;
    const StatusDeleted = 6;
    const StatusExternalFactor = 10;

    protected $fillable = [
        'asset_id', 'assigned_to', 'status', 'issue_description', 'resolution_note', 'started_at', 'completed_at', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'
    ];

    public function asset()
    {
        return $this->belongsTo('App\Models\Equipments\MaintenanceTicketHistory', 'asset_id', 'uuid');
    }

    public function assignedUser()
    {
        return $this->belongsTo('App\Models\User', 'assigned_to', 'id');
    }

    public function histories()
    {
        return $this->hasMany('App\Models\Equipments\MaintenanceTicketHistory', 'ticket_id', 'id');
    }
}
