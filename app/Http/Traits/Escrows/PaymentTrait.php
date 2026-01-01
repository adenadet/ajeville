<?php
namespace App\Http\Traits\Escrows;

use App\Http\Traits\General\FileManagerTrait;
use App\Models\Escrows\Payment;
use App\Models\Escrows\Transaction;
use App\Models\Finance\Bank;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
trait PaymentTrait {
    use FileManagerTrait;

    public function escrow_payments_receipt($payment_id, $type){
    
    }
    
    public function escrow_payments_create($data){
        DB::beginTransaction();

        try{
            if ($data['channel'] == 'transfer'){
                $bank = Bank::find($data['transfer']['bank_id']);
                $payment = Payment::create([
                    'transaction_id' => $data['transaction_id'], 
                    'date' => $data['transfer']['date'],
                    'amount' => $data['transfer']['amount'], 
                    'channel' => $data['channel'], 
                    'description' => 'Name: '.$data['transfer']['depositor_name'].' <br /> 
                                Date: '.$data['payment_reference'].' <br />
                                Amount: '.$data['amount'].' <br /> 
                                Bank: '.$bank->name.' <br /> 
                                Account Number: '.$bank->account_number.' <br /> 
                                Account Name: '.$bank->account_name.' <br /> 
                                Reference: ________________________________ <br /> 
                                Transaction ID: _________________________________',
                ]);
            }
            else{
                $payment = Payment::create([
                    'transaction_id' => $data['transaction_id'], 
                    'date' => $data['date'] ?? date('Y-m-d'),
                    'time_stamped' => $data['time_stamped'] ?? date('Y-m-d H:i:s'),
                    'amount' => $data['amount'], 
                    'channel' => $data['channel'], 
                    'description' => $data['transaction_id'].' | '.$data['payment_transaction'] ?? $data['transaction_id'].' | '.$data['payment_reference'],    
                ]);
            }
            
            DB::commit();
            return $payment;
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function escrow_payments_get_all($type, $specific, $detailed, $paginated, $page){
        $query = Payment::query();
                
        switch ($type){
            case 'admin':
                if (isset($specific['start_date']) && !is_null($specific['start_date'])){$query = $query->where('date', '>=', $specific['start_date']);}
                if (isset($specific['end_date']) && !is_null($specific['end_date'])){ $query = $query->where('date', '<=', $specific['end_date']);}
            
            break;
            case 'my_payments':
                $transaction_list = Transaction::where('seller_id', '=', Auth::id() ?? auth('api')->id())->pluck('unique_code')->toArray();
                
                $query = Payment::whereIn('transaction_id', $transaction_list);
                
                if (isset($specific['start_date']) && !is_null($specific['start_date'])){$query = $query->where('date', '>=', $specific['start_date']);}
                if (isset($specific['end_date']) && !is_null($specific['end_date'])){ $query = $query->where('date', '<=', $specific['end_date']);}
                
            break;
            case 'filtered':
                $question = Transaction::where('seller_id', '=', Auth::id() ?? auth('api')->id())
                ->where('status', '>=', $status ?? Transaction::StatusPaidAwaitingDelivery);
                if (isset($specific['product_id']) && !is_null($specific['product_id'])){
                    //echo($specific['product_date']);
                    $question = $question->where('product_id', '=', $specific['product_id']);
                }

                $transaction_list = $question->pluck('unique_code')->toArray();

                $query = Payment::whereIn('transaction_id', $transaction_list)->where('status', '=', 10);
                
                if (isset($specific['start_date']) && !is_null($specific['start_date'])){ 
                    $query = $query->where('date', '>=', $specific['start_date']);
                }
                if (isset($specific['end_date']) && !is_null($specific['end_date'])){ 
                    $query = $query->where('date', '<=', $specific['end_date']);
                }
            break;
        }

        $query = $detailed  ? $query->with(['transaction.broker', 'transaction.buyer', 'transaction.product', 'transaction.seller.company']) : $query->select('id', 'unique_id', 'title');
        $query = $paginated ? $query->orderBy('date', 'DESC')->paginate(50) : $query->orderBy('date', 'ASC')->get();

        return $query;
    }

    public function escrow_payments_get_by_id($type, $id, $detailed){
        switch ($type){
            case 'id': 
                $query = Payment::where('id', '=', $id);
            break;
            case 'unique_id':
                $query = Payment::where('unique_id', '=', $id);
            break;
        }

        $query = $detailed ? $query->with(['transaction']) : $query;
        return $query->first();
    }

    public function escrow_payments_search($data){}

    public function escrow_payments_transaction_payment($data, $transaction_id){}

    public function escrow_payments_update($data, $id){}

}