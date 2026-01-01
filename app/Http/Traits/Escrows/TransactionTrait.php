<?php
namespace App\Http\Traits\Escrows;

use App\Http\Traits\AI\AITrait;
use App\Http\Traits\Escrows\PaymentTrait;
use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Http\Traits\Ums\UserTrait;
use App\Http\Traits\Users\CustomerTrait;
use App\Jobs\SendPaymentBuyerMail;
use App\Jobs\SendPaymentSellerMail;
use App\Jobs\Escrows\SendTransactionCreatedBuyerMail;
use App\Mail\Escrows\Accepted as MailEscrowTransactionAccepted;
use App\Mail\Escrows\Created as MailEscrowTransactionCreated;
use App\Mail\Escrows\Rejected as MailEscrowTransactionRejected;
use App\Mail\Escrows\Paid as MailEscrowTransactionPaid;
use App\Mail\QuickPay\PaymentSellerMail;
use App\Mail\QuickPay\PaymentBuyerMail;
use App\Mail\QuickPay\PaymentBrokerMail;
use App\Models\Escrows\Payment;
use App\Models\Escrows\Transaction;
use App\Models\Escrows\TransactionActivity;
use App\Models\Escrows\TransactionMileStone;
use App\Models\Escrows\TransactionRequest;
use App\Models\Escrows\Vendor;
use App\Models\Escrows\Bank;
use App\Models\Escrows\Product;
use App\Models\Ums\Company;
use App\Models\User;
use App\Notifications\Escrows\Accepted as EscrowTransactionAccepted;
use App\Notifications\Escrows\Rejected as EscrowTransactionRejected;
use App\Notifications\Escrows\Created as EscrowTransactionCreated;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


trait TransactionTrait {
    use FileManagerTrait, LogTrait, PaymentTrait, ProductTrait, UserTrait;

    public function escrow_partners_get_count($type, $specific){
        switch ($type){
            case 'transactions':
                $query = Transaction::where('buyer_id', '=', $specific)
                    ->orWhere('seller_id', '=', $specific)
                    ->orWhere('broker_id', '=', $specific)
                    ->get();
            break;
        }

        return $query->count();
    }
    public function escrow_partners_get_all($type, $specific, $detailed, $paginated, $page){
        switch ($type){
            case 'admin':
                $partners = Transaction::all()->latest();

                $partner_ids = $partners->pluck('buyer_id')->merge($partners->pluck('seller_id'))->merge($partners->pluck('broker_id'))->unique();
                $query = User::whereIn('id', $partner_ids)->where('id', '!=', auth('api')->id());
            
            break;
            case 'all':
                $partners = Transaction::where('buyer_id', '=', auth('api')->id())
                    ->orWhere('seller_id', '=', auth('api')->id())
                    ->orWhere('broker_id', '=', auth('api')->id())
                    ->get()->latest();

                $partner_ids = $partners->pluck('buyer_id')->merge($partners->pluck('seller_id'))->merge($partners->pluck('broker_id'))->unique();
                $query = User::whereIn('id', $partner_ids)->where('id', '!=', auth('api')->id());
            break;
            case 'mine':
                $partners = Transaction::where('buyer_id', '=', auth('api')->id())
                    ->orWhere('seller_id', '=', auth('api')->id())
                    ->orWhere('broker_id', '=', auth('api')->id())
                    ->get();

                $partner_ids = $partners->pluck('buyer_id')->merge($partners->pluck('seller_id'))->merge($partners->pluck('broker_id'))->unique();
                $query = User::whereIn('id', $partner_ids)->where('id', '!=', auth('api')->id());
            break;
        }
        
        $query = $detailed ? $query->with(['area', 'branch', 'department', 'roles', 'state']) : $query;
        
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function escrow_partners_merge_multiple_user($main_user_id, $double_user_id){
        DB::beginTransaction();

        try{
            $mainUser = User::where('id', $main_user_id)->first();
            $tempUser = User::where('id', $double_user_id)->first();

            Transaction::where('broker_id', $tempUser->id)->update(['broker_id' => $mainUser->id]);
            Transaction::where('buyer_id', $tempUser->id)->update(['buyer_id' => $mainUser->id]);
            Transaction::where('seller_id', $tempUser->id)->update(['seller_id' => $mainUser->id]);

            $tempUser->delete();

            DB::commit();
            $this->log_user_activity('Escrow User Merge', $double_user_id, true);
            return $mainUser;
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function escrow_transaction_accept($data, $id){
        DB::beginTransaction();

        try{
            $transaction = Transaction::find($id);
            $buyer = User::find($transaction->buyer_id);
            $seller = User::find($transaction->seller_id);

            if ($data['decision'] == 'accept'){$transaction->status = Transaction::StatusAcceptedPaymentAwaiting;}
            else if($data['decision'] == 'reject'){$transaction->status = Transaction::StatusCancelled;} 

            $transaction->updated_by = auth('api')->id() ?? Auth::id();
            $transaction->updated_at = date('Y-m-d H:i:s');

            $transaction->save();

            TransactionActivity::create([
                'transaction_id' => $transaction->id,
                'user_id'   => Auth::id() ?? auth('api')->id(),
                'status' => 1,
                'subject' => Auth::user()->email." accepted the transaction",
                'details' => "<p> A transaction was accepted by ".Auth::user()->email.".</p> <p>Awaiting payment confirmation. </p>",  
            ]);

            DB::commit();
            $this->log_user_activity('Transaction Accept', $id, true);

            if ($data['decision'] == 'accept'){
                !is_null($buyer->email) ? Mail::to($buyer->email)->send(new MailEscrowTransactionAccepted($transaction, $buyer)) : null;
                !is_null($seller->email) ? Mail::to($seller->email)->send(new MailEscrowTransactionAccepted($transaction, $seller)): null;
            }
            else{
                //Mail::to($buyer->email)->send(new MailEscrowTransactionRejected($transaction, $buyer));
                //Mail::to($seller->email)->send(new MailEscrowTransactionRejected($transaction, $seller));
            }
            
            return $transaction;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Transaction Accept', $id, false);
            return $e->getMessage();
        }
    }

    public function escrow_transaction_cancel($id){
        DB::beginTransaction();

        try{
            $query = Transaction::find($id);
            $query->status = Transaction::StatusCancelled;
            $query->deleted_by = auth('api')->id() ?? Auth::id();
            $query->deleted_at = date('Y-m-d H:i:s');
            $query->save();
            $this->log_user_activity('Escrow Transaction Cancel', $id, true);
            DB::commit();

            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Escrow Transaction Cancel', $id, false);
            return $e->getMessage();
        }
    }

    public function escrow_transaction_complete($data, $id){
        DB::beginTransaction();

        try{
            $query = Transaction::find($id);

            TransactionActivity::create([
                'transaction_id' => $query->id,
                'user_id'   => Auth::id() ?? auth('api')->id(),
                'status' => 5,
                'subject' => Auth::user()->name ?? auth('api')->user()->name." completed a transaction",
                'details' => "<p> A transaction was completed by ".(Auth::user()->name ?? auth('api')->user()->name).".</p>",
            ]);

            $query->status = Transaction::StatusCompleted;
            $query->completed_by = auth('api')->id() ?? Auth::id();
            $query->completed_at = date('Y-m-d H:i:s');
            $query->updated_by = auth('api')->id() ?? Auth::id();
            $query->updated_at = date('Y-m-d H:i:s');
            $query->save();
            DB::commit();
            $this->log_user_activity('Escrow Transaction Complete', $id, true);

            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Escrow Transaction Complete', $id, false);
            return $e->getMessage();
        }
    }

    public function escrow_transaction_create($data){
        //Check if the transaction is a buy or sell
        DB::beginTransaction();

        try{
            $broker = null;

            //Determine the Roles
            switch ($data['product']['role']){
                case 'broker':
                break;
                case 'buyer':
                    $buyer = Auth::user() ?? auth('api')->user();
                    $seller = !is_null($data['partner']['id']) ? User::where('id', '=', $data['partner']['id'])->first() : $this->ums_user_create_temporary_user([
                        'name' => $data['partner']['name'],
                        'email' => $data['partner']['email'],
                        'phone' => $data['partner']['phone'],
                    ]);
                break;
                case 'seller':
                    $seller = Auth::user() ?? auth('api')->user();
                    $buyer = !is_null($data['partner']['id']) ? User::where('id', '=', $data['partner']['id'])->first() : $this->ums_user_create_temporary_user([
                        'name' => $data['partner']['name'],
                        'email' => $data['partner']['email'],
                        'phone' => $data['partner']['phone'],
                    ]);
                break;
            }

            //echo $data['product']['role'];
            //Determine the Product ID
            if (is_null($data['product']['id'])){
                $product = $this->escrow_products_create($data['product']);
            }
            else{
                $product = $this->escrow_products_get_by_id($data['product']['id']);
            }
            
            $other_details = [
                'inspection_period' => $data['inspection_period'] ?? $product->category->max_hold_period,
            ];

            $transaction = Transaction::create([
                'amount'                => $data['product']['unit_price'] * $data['product']['quantity'],
                'category_id'           => $product->category_id,
                //'contract'              => $this->ai_generate_contract($buyer, $seller, $broker, $product, $other_details),
                'date'                  => $data['date'] ?? date('Y-m-d'),
                'details'               => $data['details'],
                'title'                 => 'Transaction Contract on '.$product->description,
                'request_id'            => $data['request_id'] ?? null,
                'buyer_id'              => $buyer->id,
                'seller_id'             => $seller->id,
                'broker_id'             => null, //in future replace with this to check for broker !(is_null($broker)) ? $broker->id : null ,
                'product_id'            => $product->id,
                'invoice_id'            => $data['invoice_id'] ?? null,
                'item_details'          => $data['item_details'] ?? null,
                'inspection_period'     => $data['inspection_period'] ?? 30,
                'unique_code'           => $this->escrow_transaction_create_code(15, true),
                'confirmation_code'     => $this->escrow_transaction_create_code(6, false),
                'status'                => Transaction::StatusPending,
                'created_by'            => auth('api')->id() ?? Auth::id(),
                'updated_by'            => auth('api')->id() ?? Auth::id(),
            ]);
            //Send a notification to the seller if initiated by buyer and vice versa

            if (isset($data['milestones'])){
                foreach ($data['milestones'] as $milestone){
                    TransactionMileStone::create([
                        'description' => $milestone['description'], 
                        'transaction_id' => $transaction->id, 
                        'transaction_request_id' => null, 
                        'completion_level' => $milestone['completion_level'], 
                        'created_by' => auth('api')->id() ?? Auth::id(), 
                        'updated_by' => auth('api')->id() ?? Auth::id(),
                    ]);
                }
            }
            else{
                TransactionMileStone::create([
                    'description' => 'Auto generated milestone', 
                    'transaction_id' => $transaction->id, 
                    'transaction_request_id' => null, 
                    'completion_level' => '100', 
                    'created_by' => auth('api')->id() ?? Auth::id(), 
                    'updated_by' => auth('api')->id() ?? Auth::id(),
                ]);
            }
            
            TransactionActivity::create([
                'transaction_id' => $transaction->id,
                'user_id'   => Auth::id() ?? auth('api')->id(),
                'status' => 1,
                'subject' => Auth::user()->email." started a transaction",
                'details' => "<p> A transaction was created for ".$product->description." by ".Auth::user()->email.".</p> <p>Awaiting partner confirmation. </p>",  
            ]);

            $this->log_user_activity('Escrow Transaction Create', $transaction->id, true);
            DB::commit();

            //dispatch(new SendTransactionCreatedBuyerMail($transaction, $buyer, $seller));
            Mail::to($buyer->email)->send(new MailEscrowTransactionCreated($transaction, $buyer));
            Mail::to($seller->email)->send(new MailEscrowTransactionCreated($transaction, $seller));
            
            return $transaction;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Escrow Transaction Create', null, false);
            return $e->getMessage();
        }
    }

    public function escrow_transaction_direct($data){
        DB::beginTransaction();

        try{
            $seller = Vendor::where('uuid', '=', $data['vendor_id'])->first();
            $product = Product::where('product_id', '=', $data['product'])->first();
            
            $user = User::where('email', '=', $data['email'])->first(); 
            $buyer = $this->user_create_temporary_user([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
            ]);

            $transaction = Transaction::create([
                'amount'                => $data['amount'],
                'category_id'           => $product->category_id,
                'date'                  => $data['date'] ?? date('Y-m-d'),
                'details'               => $data['details'],
                'title'                 => 'Transaction Contract on '.$product->description,
                'request_id'            => $data['request_id'] ?? null,
                'buyer_id'              => $user->id ?? $buyer->id,
                'seller_id'             => $seller->user_id,
                'broker_id'             => null,
                'product_id'            => $product->id,
                'invoice_id'            => $data['invoice_id'] ?? null,
                'item_details'          => $data['item_details'] ?? null,
                'inspection_period'     => $data['inspection_period'] ?? 30,
                'unique_code'           => $this->escrow_transaction_create_code(15, true),
                'confirmation_code'     => $this->escrow_transaction_create_code(6, false),
                'status'                => 4,
                'created_by'            => auth('api')->id(),
                'updated_by'            => auth('api')->id(),
            ]);
            
            TransactionActivity::create([
                'transaction_id' => $transaction->id,
                'user_id'   => $buyer->id,
                'status' => 1,
                'subject' => $buyer->full_name." started a transaction",
                'details' => "<p> A transaction was created for ".$product->description." by ".$buyer->full_name.".</p> <p>Awaiting partner confirmation. </p>",
            ]);

            $this->log_user_activity('Escrow Transaction Create', $transaction->id, true);
            DB::commit();

        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Escrow Transaction Create', null, false);
            return $e->getMessage();
        }
    }
    public function escrow_transaction_get_all($type, $specific, $detailed, $paginated, $page){
        $query = Transaction::query();

        switch ($type){
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'admin':
                $query = $query->orderBy('status', 'ASC');

            break;
            case 'mine':
                $query = $query->where('buyer_id', '=', Auth::id() ?? auth('api')->id())
                ->orWhere('seller_id', '=', Auth::id() ?? auth('api')->id());
            break;
            case 'my_filtered_payments':
                $question = $query->where('seller_id', '=', Auth::id() ?? auth('api')->id())
                ->where('status', '>=', $status ?? Transaction::StatusPaidAwaitingDelivery);
                if (isset($specific['product_id']) && !is_null($specific['product_id'])){
                    //echo($specific['product_date']);
                    $question = $question->where('product_id', '=', $specific['product_id']);
                }

                $transaction_list = $question->pluck('unique_code')->toArray();

                $query = Payment::whereIn('transaction_id', $transaction_list)->where('status', '=', 10);
                
                if (isset($specific['start_date']) && !is_null($specific['start_date'])){ 
                    //echo($specific['start_date']);
                    $query = $query->where('date', '>=', $specific['start_date']);
                }
                if (isset($specific['end_date']) && !is_null($specific['end_date'])){ 
                    //echo($specific['end_date']);
                    $query = $query->where('date', '<=', $specific['end_date']);
                }
                $query = $query->latest();
            break;
            case 'my_filtered_transactions':
                $query = $query->where('seller_id', '=', Auth::id() ?? auth('api')->id());

                /*
                if (isset($specific['product_id']) && !is_null($specific['product_id'])){
                    $query = $query->where('product_id', '=', $specific['product_id']);
                }
                if (isset($specific['start_date']) && !is_null($specific['start_date'])){ 
                    $query = $query->where('date', '>=', $specific['start_date']);
                }
                if (isset($specific['end_date']) && !is_null($specific['end_date'])){ 
                    $query = $query->where('date', '<=', $specific['end_date']);
                }
                if(isset($specific['status']) && !is_null($specific['status']) && $specific['status'] != 'all'){
                    switch($specific['status']){
                        case 'accepted':
                            $query = $query->whereIn('status',  [Transaction::StatusAwaitingConfirmationBuyer, Transaction::StatusAwaitingConfirmationSeller, Transaction::StatusAwaitingConfirmationBroker, Transaction::StatusAcceptedPaymentAwaiting]);
                        break;
                        case 'cancelled':
                            $query = $query->where('status', '>=', Transaction::StatusCancelled)->where('status', '<=', 0111);
                        break;
                        case 'completed':
                            $query = $query->where('status', '=', Transaction::StatusCompleted);
                        break;
                        case 'disputed':
                            $query = $query->whereIn('status', [Transaction::StatusDisputed, Transaction::StatusDisputedByBroker, Transaction::StatusDisputedByBuyer, Transaction::StatusDisputedBySeller]);
                        break;
                        case 'ongoing':
                            $query = $query->whereIn('status', [Transaction::StatusPaidAwaitingDelivery,Transaction::StatusPaidAwaitingDelivery, Transaction::StatusOngoing, Transaction::StatusDeliveredAwaitingBuyer, Transaction::StatusDeliveredAwaitingSeller, Transaction::StatusDeliveredAwaitingBroker]);
                        break;
                        case 'paid':
                            $query = $query->where('status', '=', Transaction::StatusPaidAwaitingDelivery);
                        break;
                        case 'pending':
                            $query = $query->whereIn('status',[Transaction::StatusAcceptedPaymentAwaiting, Transaction::StatusPending, Transaction::StatusAwaitingConfirmationBroker]);
                        break;
                    }
                }*/
            break;
            
            case 'my_payments':
                $query = $query->where('seller_id', '=', Auth::id() ?? auth('api')->id())->where('status', '>=', $specific['status'] ?? Transaction::StatusPaidAwaitingDelivery);
            break;
            case 'product':
                $query = $query->where('product_id', '=', $specific);
            break;
        }

        
        if(is_array($specific)){
            if (isset($specific['start_date']) && !empty($specific['start_date'])){ 
                $query = $query->where('date', '>=', $specific['start_date']);
            }
            if (isset($specific['end_date']) && !empty($specific['end_date'])){ 
                $query = $query->where('date', '<=', $specific['end_date']);
            }
            if(isset($specific['status']) && !empty($specific['status']) && $specific['status'] != 'all'){
                switch($specific['status']){
                    case 'accepted':
                        $query = $query->whereIn('status',  [Transaction::StatusAwaitingConfirmationBuyer, Transaction::StatusAwaitingConfirmationSeller, Transaction::StatusAwaitingConfirmationBroker, Transaction::StatusAcceptedPaymentAwaiting]);
                    break;
                    case 'cancelled':
                        $query = $query->where('status', '>=', Transaction::StatusCancelled)->where('status', '<=', 0111);
                    break;
                    case 'completed':
                        $query = $query->where('status', '=', Transaction::StatusCompleted);
                    break;
                    case 'disputed':
                        $query = $query->whereIn('status', [Transaction::StatusDisputed, Transaction::StatusDisputedByBroker, Transaction::StatusDisputedByBuyer, Transaction::StatusDisputedBySeller]);
                    break;
                    case 'ongoing':
                        $query = $query->whereIn('status', [Transaction::StatusPaidAwaitingDelivery,Transaction::StatusPaidAwaitingDelivery, Transaction::StatusOngoing, Transaction::StatusDeliveredAwaitingBuyer, Transaction::StatusDeliveredAwaitingSeller, Transaction::StatusDeliveredAwaitingBroker]);
                    break;
                    case 'paid':
                        $query = $query->where('status', '=', Transaction::StatusPaidAwaitingDelivery);
                    break;
                    case 'pending':
                        $query = $query->whereIn('status',[Transaction::StatusAcceptedPaymentAwaiting, Transaction::StatusPending, Transaction::StatusAwaitingConfirmationBroker]);
                    break;
                }
            }
        }

        if(isset($specific['query']) && !empty($specific['query'])){
            $search = $specific['query'];
            $users = User::where(function($query) use ($search){
                $query->where('first_name', 'LIKE', "%$search%")
                    ->orWhere('middle_name', 'LIKE', "%$search%")
                    ->orWhere('last_name', 'LIKE', "%$search%")
                    ->orWhere('email', 'LIKE', "%$search%");
                })->pluck('id');
        
            $query = $query->where('title', 'LIKE', "%$search%")
                ->orWhere('unique_code', 'LIKE', "%$search%")
                ->orWhere('details', 'LIKE', "%$search%")
                ->orWhereIn('buyer_id', $users)
                ->orWhereIn('seller_id', $users);
        }
        
        $query = $detailed  ? $query->with(['broker', 'buyer', 'product', 'seller', 'payment']) : $query->select('id', 'unique_code', 'title');
        $query = $paginated ? $query->latest()->paginate(50) : $query->get();

        return $query;
    }

    public function escrow_transaction_get_by($type, $id, $detailed){
        switch($type){
            case 'id':
                $query = Transaction::where('id', '=', $id);
            break;
            case 'unique_code':
                $query = Transaction::where('unique_code', '=', $id);
            break;
        }

        $query = $detailed  ? $query->with(['activities.user', 'broker', 'buyer', 'product', 'seller', 'payment']) : $query->select('id', 'unique_id', 'title');
        
        return $query->first(); 
    }

    public function escrow_transaction_quick_create($data){
        DB::beginTransaction();

        try{
            $buyer = $this->user_create_temporary_user([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
            ]);
            if (!isset($data['seller'])){
                $seller_company = Company::where('uuid', '=', $data['vendor_id'])->firstOrFail();
                $seller = User::where('id', '=', $seller_company->user_id)->firstOrFail();    
            }
            else{
                $seller = $data['seller'];
            }
            
            $transaction = Transaction::create([
                'amount'                => $data['amount'],
                'category_id'           => 0,
                'date'                  => $data['date'] ?? date('Y-m-d'),
                'details'               => $data['details'],
                'title'                 => 'Direct pay from App',
                'request_id'            => $data['request_id'] ?? null,
                'buyer_id'              => $buyer['id'],
                'seller_id'             => $seller['id'],
                'broker_id'             => null,
                'product_id'            => $data['product_id'] ?? 0,
                'invoice_id'            => $data['unique_id'] ?? null,
                'item_details'          => $data['item_details'] ?? null,
                'inspection_period'     => $data['inspection_period'] ?? 0,
                'unique_code'           => $data['unique_id'] ??$this->escrow_transaction_create_code(15, true),
                'confirmation_code'     => $this->escrow_transaction_create_code(6, false) ?? null,
                'status'                => Transaction::StatusAcceptedPaymentAwaiting,
                'created_by'            => 0,
                'updated_by'            => 0,
            ]);

            DB::commit();
            $this->log_user_activity('Escrow Quick Transaction Create', $transaction->id, true);
            $detailed_transaction = $this->escrow_transaction_get_by('id', $transaction->id, true);
            return $detailed_transaction;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity('Escrow Quick Transaction Create', null, false);
            return $e->getMessage();
        }
    }

    public function escrow_transaction_quick_complete($data, $id){
        DB::beginTransaction();

        try{
            $transaction = $this->escrow_transaction_get_by('unique_code', $id, true);
            $data['transaction_id'] = $id;
            $payment = $this->escrow_payments_create($data);
            if (is_string($payment)){
                return 'Error: '.$payment; 
            }
            $transaction->status = Transaction::StatusPaidAwaitingDelivery;
            $transaction->save();

            DB::commit();
            
            $broker = User::where('id', '=', $transaction->broker_id)->with(['company'])->first();
            $buyer = User::where('id', '=', $transaction->buyer_id)->with(['company'])->first();
            $seller = User::where('id', '=', $transaction->seller_id)->with(['company'])->first();
            
            //Send Mail to Buyer
            //dispatch(new SendPaymentBuyerMail($transaction, $buyer, $seller, $payment));
            //dispatch(new SendPaymentSellerMail($transaction, $buyer, $seller, $payment));
            Mail::to($buyer->email)->send(new PaymentBuyerMail($transaction, $buyer, $seller, $payment));
            Mail::to($seller->company->email ?? $seller->email)->send(new PaymentSellerMail($transaction, $buyer, $seller, $payment));
            
            //Send Mail to Broker if it exists
            if (!(is_null($transaction->broker_id)) && !(is_null($transaction->broker))){
                
            }
            return $transaction;
        }
        catch (Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function escrow_transaction_reminder_mail($id){
        try{
            $transaction = $this->escrow_transaction_get_by('id', $id, true);
            $buyer = User::where('id', '=', $transaction->buyer_id)->with(['company'])->first();
            $seller = User::where('id', '=', $transaction->seller_id)->with(['company'])->first();
        
            if ($buyer->id == auth('api')->id() ?? Auth::id()){
                Mail::to($seller->email)->send(new MailEscrowTransactionCreated($transaction, $seller));
            }
            else{
                Mail::to($buyer->email)->send(new MailEscrowTransactionCreated($transaction, $buyer));
            }
            
            return true;
        }
        catch (Exception $e){
            return $e->getMessage();
        }
    }
    public function escrow_transaction_update($data, $id){
        DB::beginTransaction();

        try{    
            $broker = null;
            switch ($data['role']){
                case 'broker':
                break;
                case 'buyer':
                    $buyer = Auth::user() ?? auth('api')->user();
                    $seller = !is_null($data['partner_id']) ? User::where('unique_id', '=', $data['partner_id'])->first() : $this->user_create_temporary_user([
                        'name' => $data['partner_name'],
                        'email' => $data['partner_email'],
                        'phone' => $data['partner_phone'],
                    ]);
                break;
                case 'seller':
                    $seller = Auth::user() ?? auth('api')->user();
                    $buyer = !is_null($data['partner_id']) ? User::where('unique_id', '=', $data['partner_id'])->first() : $this->user_create_temporary_user([
                        'name' => $data['partner_name'],
                        'email' => $data['partner_email'],
                        'phone' => $data['partner_phone'],
                    ]);
                break;
            }

            $query = Transaction::find($id);
            
            $query->amount                = $data['amount'];
            $query->buyer_id              = $buyer->id;
            $query->category_id           = $data['category_id'];
            $query->confirmation_code     = $this->escrow_transaction_create_code(6, false);
            $query->date                  = $data['date'] ?? date('Y-m-d');
            $query->details               = $data['details'] ?? null;
            $query->inspection_period     = $data['inspection_period'] ?? 30;
            $query->invoice_id            = $data['invoice_id'] ?? null;
            $query->item_details          = $data['item_details'] ?? null;
            $query->product_id            = $data['product_id'] ?? null;
            $query->request_id            = $data['request_id'] ?? null;
            $query->seller_id             = $seller->id;
            $query->status                = $data['status'] ?? Transaction::StatusAcceptedPaymentAwaiting;
            $query->title                 = $data['title'];
            $query->unique_code           = $this->escrow_transaction_create_code(15, true);
            $query->updated_by            = auth('api')->id();
            
            $query->save();
            
            $this->log_user_activity('Escrow Transaction Update', $id, true);
            DB::commit();

            
            //Send an email/sms to the customers
            //$buyer->notify(new EscrowTransactionCreated($query, $buyer));
            //$seller->notify(new EscrowTransactionCreated($query, $seller));
            
            
            //Log the transaction
        
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Escrow Transaction Update', $id, false);
            return $e->getMessage();
        }
    }

    /*
    ---------------------------------------------------
    Escrow Transaction Payment CRUD
    ---------------------------------------------------
    */
    public function escrow_transaction_payment_confirm($data, $transaction_id, $channel = 'transfer'){
        DB::beginTransaction();

        try{
            $transaction = Transaction::find($transaction_id);
            $buyer = User::find($transaction->buyer_id);
            $seller = User::find($transaction->seller_id);

            $payment = $this->escrow_payments_create($data);
            
            TransactionActivity::create([
                'transaction_id' => $transaction_id,
                'user_id'   => Auth::id() ?? auth('api')->id(),
                'status' => 5,
                'subject' => Auth::user()->name ?? auth('api')->user()->name." paid for the transaction"
            ]);

            DB::commit();
            $this->log_user_activity('Escrow Transaction Payment Create', $payment->id, true);

            Mail::to($buyer->email)->send(new MailEscrowTransactionPaid($transaction, $buyer));
            Mail::to($seller->email)->send(new MailEscrowTransactionPaid($transaction, $seller));
        
            return $payment;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Escrow Transaction Payment Create', null, false);
            return $e->getMessage();
        }
    }

    public function escrow_transaction_payment_create($data, $transaction_id, $channel = 'transfer'){
        DB::beginTransaction();

        try{
            $transaction = $this->escrow_transaction_get_by('unique_code', $transaction_id, true);
            $buyer = User::find($transaction->buyer_id);
            $seller = User::find($transaction->seller_id);

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
                    'date' => $data['date'],
                    'amount' => $data['amount'], 
                    'channel' => $data['channel'], 
                    'description' => $data['payment_transaction'],    
                ]);
        
            }
            
            TransactionActivity::create([
                'transaction_id' => $transaction_id,
                'user_id'   => Auth::id() ?? auth('api')->id(),
                'status' => 5,
                'subject' => Auth::user()->name ?? auth('api')->user()->name." paid for the transaction"
            ]);

            DB::commit();
            $this->log_user_activity('Escrow Transaction Payment Create', $payment->id, true);

            Mail::to($buyer->email)->send(new MailEscrowTransactionPaid($transaction, $buyer));
            Mail::to($seller->email)->send(new MailEscrowTransactionPaid($transaction, $seller));
        
            return $payment;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Escrow Transaction Payment Create', null, false);
            return $e->getMessage();
        }
    }
    public function escrow_transaction_payment_get_all($type, $specific, $detailed, $paginated, $page){
        switch ($type){
            case 'all':
                $query = Payment::withTrashed();
            break;
            case 'confirmed':
                $query = Payment::whereNotNull('confirmed_by')->where('status', '=', 1);
                break;
            case 'confirmer':
                $query = Payment::where('confirmed_by', '=', $specific);
                break;
            case 'unconfirmed':
                $query = Payment::where('status', '=', 0);
            break;
        }

        $query = $detailed  ? $query->with(['transaction.buyer', 'transaction.seller', 'transaction.product', 'confirmer']) : $query->select('id', 'unique_id', 'title');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function escrow_transaction_request_create_new($data){
        
        //if type="seller_new_item"
    }

    public function escrow_transaction_request_cancel($data){
        
    }

    public function escrow_transaction_request_deactivate($id){
        
    }

    public function escrow_transaction_request_get_all_requests($type, $specific, $detailed, $paginated, $page){
        
    }

    public function escrow_transaction_request_get_by_id($id){}

    public function escrow_transaction_request_update_request($data, $id){}


    public function escrow_transaction_milestone_create_new($data, $transaction_id){}

    
    private function escrow_generate_code($n = 10, $alphanumeric){
        $my_string = $alphanumeric ? 'ABCDEFGHILJKLMNOPQRSTUVWXYZ0123456789': '0123456789';     
        $my_random_string = str_repeat($my_string, 10);     
        $my_random_string = str_shuffle($my_random_string);     
        $my_random_string = substr($my_random_string, 0, $n);     
        return $my_random_string; 
    }



    private function escrow_transaction_create_code($n = 15, $alphanumeric = true){
        $unique_ref_found = false;
        while (!$unique_ref_found) {  
            $digital = $this->escrow_generate_code($n, $alphanumeric);  
            $num = Transaction::where('unique_code', '=', $digital)->count();
            if ($num==0) {  
                $unique_ref_found = true;  
            }
        }
        return $digital; 
    }
}