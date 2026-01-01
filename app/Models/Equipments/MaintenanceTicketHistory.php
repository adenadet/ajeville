<?php

namespace App\Models\Equipments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceTicketHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id', 'created_by', 'updated_by', 'change_type', 'notes', 'user_assigned_to',  'department_assigned_to', 'new_status'
    ];

    public function ticket()
    {
        return $this->belongsTo('App\Models\Equipments\MaintenanceTicket', 'ticket_id');
    }

    public function creater()
    {
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }

    public function updater()
    {
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }

    public function newAssignee()
    {
        return $this->belongsTo('App\Models\User', 'user_assigned_to', 'id');
    }

    public function newDepartmentAssignee()
    {
        return $this->belongsTo('App\Models\Operations\Department', 'department_assigned_to', 'id');
    }
}
