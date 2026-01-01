<?php
namespace App\Http\Traits\Escrows;

use App\Http\Traits\General\FileManagerTrait;
use App\Models\Escrows\Dispute;
use App\Models\Escrows\DisputeAction;
use App\Models\Escrows\Transaction;
use App\Models\User;
use App\Notifications\Escrows\TransactionDisputed as EscrowTransactionDisputed;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
trait DisputeTrait {
    use FileManagerTrait;

    public function escrow_dispute_create($data){
        DB::beginTransaction();
        try{
            $transaction = Transaction::where('unique_code', '=', $data['transaction_id'])->orWhere('id', '=', $data['transaction_id'])->first();
            
            //Create a new Dispute
            $query = Dispute::create([
                'transaction_id' => $transaction->id,
                'ticket_id' => $data['ticket_id'] ?? null,
                'subject' => $data['subject'],
                'content' => $data['content'],
                'status' => $data['status'],
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);    
            DB::commit();
            $this->log_user_activity('Transaction Dipute Created', $query->id, true);
        
            //Send an email/sms to the customers
            $buyer = User::find($transaction->buyer_id);
            $seller = User::find($transaction->seller_id);
            //Send a real notification
            $buyer->notify(new EscrowTransactionDisputed($transaction, $query));
            $seller->notify(new EscrowTransactionDisputed($transaction, $query));
            //Log the transaction
        
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Transaction Dipute Created', null, false);
            return $e->getMessage();
        }
    }

    public function escrow_dispute_delete($id){
        DB::beginTransaction();
        try{    
            $query = Dispute::find($id);
            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->deleted_by = Auth::id() ?? auth('api')->id();
            $query->status = 10; //i.e. resolved
            $query->save();

            $transaction = Transaction::find($query->transaction_id);
            $transaction->disputed = 0;
            $transaction->save();

            DB::commit();
            $this->log_user_activity('Transaction Dipute Created', $query->id, true);
        
            //Send an email/sms to the customers
            $buyer = User::find($transaction->buyer_id);
            $seller = User::find($transaction->seller_id);
            //Send a real notification
            $buyer->notify(new EscrowTransactionDisputed($transaction, $query));
            $seller->notify(new EscrowTransactionDisputed($transaction, $query));
            //Log the transaction
        
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Transaction Dipute Created', null, false);
            return $e->getMessage();
        }
    }

    public function escrow_dispute_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'all':
                $query = Dispute::withTrashed();
            break;
            case 'active':
                $query = Dispute::where('status', '<=', 5);
            break;
            case 'mine':
                $transactions = Transaction::where('buyer_id', '=', Auth::id() ?? auth('api')->id())->orWhere('seller_id', '=', Auth::id() ?? auth('api')->id())->pluck('id');
                $query = Dispute::whereIn('transaction_id', $transactions);
            break;
        }

        $query = $query->orderBy('created_at', 'DESC');
        $query = $detailed  ? $query->with(['creator', 'deleter', 'transaction.buyer', 'transaction.seller', 'ticket', 'updater']) : $query->select('id', 'subject', 'status');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function escrow_dispute_get_by($type, $id, $detailed){
        $query = Dispute::wheere('id', '=', $id);
        $query = $detailed  ? $query->with(['creator', 'deleter', 'transaction.buyer', 'transaction.seller', 'ticket', 'updater']) : $query->select('id', 'subject', 'status');
        
        return $query->first();
    }

    public function escrow_dispute_update($data, $id){
        DB::beginTransaction();

        try{
            $transaction = Transaction::where('unique_code', '=', $data['transaction_id'])->orWhere('id', '=', $data['transaction_id'])->first();
            $query = Dispute::find($id);
            $query->transaction_id = $transaction->id;
            $query->ticket_id = $data['ticket_id'] ?? null;
            $query->subject = $data['subject'];
            $query->content = $data['content'];
            $query->status = $data['status'];
            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->save();
            DB::commit();
            $this->log_user_activity('Transaction Dipute Updated', $query->id, true);
        
            //Send an email/sms to the customers
            $buyer = User::find($transaction->buyer_id);
            $seller = User::find($transaction->seller_id);
            //Send a real notification
            $buyer->notify(new EscrowTransactionDisputed($transaction, $query));
            $seller->notify(new EscrowTransactionDisputed($transaction, $query));
            //Log the transaction
        
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Transaction Dipute Updated', null, false);
            return $e->getMessage();
        }
    }

    public function escrow_dispute_action_create($data){
        DB::beginTransaction();

        try{
            $dispute = Dispute::find($data['dispute_id']);
            $transaction = Transaction::find($dispute->transaction_id);
            $query = DisputeAction::create([
                'dispute_id' => $data['dispute_id'],
                'user_id' => Auth::id() ?? auth('api')->id(),
                'type_id' => $data['type_id'],
                'comment' => $data['comment'],
                'status' => $data['status'],
            ]);
            
            DB::commit();
            $this->log_user_activity('Transaction Dipute Created', $query->id, true);
        
            //Send an email/sms to the customers
            $buyer = User::find($transaction->buyer_id);
            $seller = User::find($transaction->seller_id);
            //Send a real notification
            $buyer->notify(new EscrowTransactionDisputed($transaction, $query));
            $seller->notify(new EscrowTransactionDisputed($transaction, $query));
            //Log the transaction
        
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Transaction Dipute Created', null, false);
            return $e->getMessage();
        }
    }

    public function escrow_dispute_action_update($data, $id){
        DB::beginTransaction();

        try{
            $dispute = Dispute::find($data['dispute_id']);
            $transaction = Transaction::find($dispute->transaction_id);
            $query = DisputeAction::create([
                'dispute_id' => $data['dispute_id'],
                'user_id' => Auth::id() ?? auth('api')->id(),
                'type_id' => $data['type_id'],
                'comment' => $data['comment'],
                'status' => $data['status'],
            ]);
            
            DB::commit();
            $this->log_user_activity('Transaction Dipute Created', $query->id, true);
        
            //Send an email/sms to the customers
            $buyer = User::find($transaction->buyer_id);
            $seller = User::find($transaction->seller_id);
            //Send a real notification
            $buyer->notify(new EscrowTransactionDisputed($transaction, $query));
            $seller->notify(new EscrowTransactionDisputed($transaction, $query));
            //Log the transaction
        
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Transaction Dipute Created', null, false);
            return $e->getMessage();
        }
    }
}