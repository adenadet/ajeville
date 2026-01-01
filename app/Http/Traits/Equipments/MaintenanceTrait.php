<?php
namespace App\Http\Traits\Equipments;

use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\Equipments\Asset;
use App\Models\Equipments\AssetType;
use App\Models\Equipments\AssignmentRegister;
use App\Models\Equipments\MaintenanceTicket;
use App\Models\Equipments\MaintenanceTicketHistory;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

trait MaintenanceTrait {
    use FileManagerTrait, LogTrait;

    private function generate_repair_uuid()
    {
        return 'REPAIR-' . strtoupper(uniqid());
    }

    /*
    |--------------------------------------------------------------------------
    | Basic Maintenance Ticket Functions
    |--------------------------------------------------------------------------
    */

    public function equipment_maintenance_ticket_assign($data, $id){
        DB::beginTransaction();

        try{
            $query = MaintenanceTicket::where('id', $id)->firstOrFail();
            
            $query->assigned_to = $data['assigned_to'] ?? null;
            $query->department_id = $data['department_id'] ?? null;
            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->status = $data['status'] ?? MaintenanceTicket::StatusAssigned;
            $query->current_descritpion = "Ticket has been assigned";

            $query->save();

            MaintenanceTicketHistory::create([
                'ticket_id' => $query->id,
                'status' => $data['status'] ?? MaintenanceTicket::StatusAssigned,
                'description' => $data['current_descritpion'] ?? "Ticket has been assigned",
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            $this->user_log_activity('Maintenance Ticket Assigned', $id, false);
        }
        catch(Exception $e){
            DB::rollBack();
            $this->user_log_activity('Maintenance Ticket Assigned', $id, false);
            return $e->getMessage();
        }
    }

    public function equipment_maintenance_ticket_create($data){
        //Create a new Maintenance Ticket
        DB::beginTransaction();        
        try{
            $ticket = MaintenanceTicket::create([
                'asset_id' => $data['asset_id'],
                'assigned_to' => $data['assigned_to'],
                'status' => $data['status'] ?? MaintenanceTicket::StatusOpen,
                'issue_description' => $data['issue_description'],
                'started_at' => null,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            //Send an email/sms to the customers
            
            //Log the transaction
            $this->user_log_activity('Maintenance Ticket Created', $ticket->id, true);
            DB::commit();

            // Mail::to($data['assigned_to'])->send(new MaintenanceTicketCreated($ticket));

            return $ticket;
        }  
        catch(Exception $e){
            DB::rollback();
            $this->user_log_activity('Maintenance Ticket Created', null, false);
            return $e->getMessage();
        };

        
    }

    public function equipment_maintenance_ticket_close($id){
        DB::beginTransaction();
        try{
            $query = MaintenanceTicket::where('id', $id)->firstOrFail();
            $query->status = MaintenanceTicket::StatusClosed;
            $query->current_descritpion = "Ticket has been closed";
            $query->ended_date = date('Y-m-d H:i:s');
            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->save();

            MaintenanceTicketHistory::create([
                'ticket_id' => $query->id,
                'status' => $data['status'] ?? MaintenanceTicket::StatusClosed,
                'description' => $data['current_descritpion'] ?? "Ticket has been closed",
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            //Log the transaction
            $this->user_log_activity('Maintenance Ticket Closed', $id, true);
            DB::commit();
        }  
        catch(Exception $e){
            DB::rollback();
            $this->user_log_activity('Maintenance Ticket Closed', null, false);
            return $e->getMessage();
        };
    }

    public function equipment_maintenance_ticket_get_all($type, $specific, $detailed, $paginated, $page){
        //Get all Maintenance Tickets
        switch ($type){
            case 'all':
                $query = MaintenanceTicket::with(['asset', 'assignedUser']);
            break;
            case 'asset':
                $query = MaintenanceTicket::where('asset_id', $specific);
            break;
            case 'assigned':
                $query = MaintenanceTicket::where('assigned_to', $specific);
            break;
            case 'status':
                $query = MaintenanceTicket::where('status', $specific);
            break;
        }

        $query = $detailed ? $query->with([]) : $query->select('uuid', 'asset_id', 'status')->with(['asset']);

        $query->orderBy('started_date', 'DESC')->orderBy('created_at', 'DESC');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function equipment_maintenance_ticket_deactivate($id){
        //Deactivate a Maintenance Ticket
        try{
            $query = MaintenanceTicket::where('id', $id)->firstOrFail();
            $query->status = MaintenanceTicket::StatusDeleted;
            $query->current_descritpion = "Ticket has been deactivated";
            $query->ended_date = date('Y-m-d H:i:s');
            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->save();

            MaintenanceTicketHistory::create([
                'ticket_id' => $query->id,
                'status' => $data['status'] ?? MaintenanceTicket::StatusDeleted,
                'description' => $data['current_descritpion'] ?? "Ticket has been deactivated",
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            //Log the transaction
            $this->user_log_activity('Maintenance Ticket Deactivated', $id, true);
            DB::commit();
        }  
        catch(Exception $e){
            DB::rollback();
            $this->user_log_activity('Maintenance Ticket Deactivated', null, false);
            return $e->getMessage();
        };
    }

    public function equipment_maintenance_ticket_update($data, $id){
        DB::beginTransaction();

        try{
            $query = MaintenanceTicket::where('id', $id)->firstOrFail();
            
            $query->assigned_to = $data['assigned_to'] ?? null;
            $query->department_id = $data['department_id'] ?? null;
            $query->current_descritpion = "Ticket has been assigned";
            $query->status = $data['status'] ?? MaintenanceTicket::StatusAccepted;
            $query->updated_by = Auth::id() ?? auth('api')->id();
            
            $query->save();

            MaintenanceTicketHistory::create([
                'ticket_id' => $query->id,
                'status' => $data['status'] ?? MaintenanceTicket::StatusAccepted,
                'description' => $data['current_descritpion'] ?? "Ticket has been assigned",
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            $this->user_log_activity('Maintenance Ticket Assigned', $id, false);
        }
        catch(Exception $e){
            DB::rollBack();
            $this->user_log_activity('Maintenance Ticket Assigned', $id, false);
            return $e->getMessage();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Basic Maintenance Ticket Functions
    |--------------------------------------------------------------------------
    */
    public function equipment_maintenance_ticket_history_create($data){
        DB::beginTransaction();

        try{
            $ticket = MaintenanceTicketHistory::create([
                'ticket_id' => $data['ticket_id'],
                'status' => $data['status'] ?? MaintenanceTicket::StatusAccepted,
                'description' => $data['description'],
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            //Send an email/sms to the customers
            
            //Log the transaction
            $this->user_log_activity('Maintenance Ticket History Created', $ticket->id, true);
            DB::commit();

            // Mail::to($data['assigned_to'])->send(new MaintenanceTicketCreated($ticket));

            return $ticket;
        }  
        catch(Exception $e){
            DB::rollback();
            $this->user_log_activity('Maintenance Ticket History Created', null, false);
            return $e->getMessage();
        };
    }
}