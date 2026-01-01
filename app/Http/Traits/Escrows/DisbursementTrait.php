<?php
namespace App\Http\Traits\Escrows;

use App\Http\Traits\General\FileManagerTrait;
use App\Models\Escrows\Disbursement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
trait DisbursementTrait {
    use FileManagerTrait;

    public function escrow_disbursement_create_new($data){
        $query = [];
        //Create a new Disbursement

        //Send an email/sms to the customers

        //Send a real notification

        //Log the transaction
        return $query;
    }

    public function escrow_disbursement_delete($data){
        $query = [];
        //Create a new Disbursement

        //Send an email/sms to the customers

        //Send a real notification

        //Log the transaction
        return $query;
    }

    public function escrow_disbursement_get_all($type, $specific, $detailed, $paginated, $page){
        $query = Disbursement::query();
        switch ($type){
            case 'all':
                $query = $query->latest();
            break;
            case 'pending':
                $query = $query->where('status', '=', Disbursement::StatusPending)->latest();
            break;
            case 'mine':
                $query = $query->where('user_id', '=', Auth::id() ?? auth('api')->id())->latest();
            break;
            case 'my_pending':
                $query = $query->where('user_id', '=', Auth::id() ?? auth('api')->id())->where('status', '=', Disbursement::StatusPending)->latest();
            break;
            case 'transaction':
                $query = $query->where('transaction_id', '=', $specific)->latest();
            break;
        }

        $query = $detailed ? $query->with(['milestone', 'transaction', 'user', 'user_account']) : $query->select('id', 'transaction_id', 'user_account_id', 'amount');
        $query = $paginated ? $query->paginate(30, ['*'], 'page', $page) : $query->get();

        return $query;
    }

    public function escrow_disbursement_get_by($type, $id, $detailed){}

    public function escrow_disbursement_update($data, $id){}
}