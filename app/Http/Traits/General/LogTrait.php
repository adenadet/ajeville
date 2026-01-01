<?php
namespace App\Http\Traits\General;

use App\Http\Traits\General\FileManagerTrait;
use App\Models\Log\Activity;
use Illuminate\Support\Facades\Auth;

trait LogTrait{
    public function log_user_activity($type, $item_id, $status){
        switch ($type){
            case 'CMS Customer Balance Updated':
                Activity::create([
                    'subject' => auth('api')->user()->first_name.' '.auth('api')->user()->last_name. ($status ? ' has successfully ' : 'unsuccessfully ').'updated customer id: '.$item_id['customer_id']. 'by '.$item_id['amount'],
                    'url' => 'CMS Customer Balance Updated',
                    'method' => 'Customer Balance', 
                    'ip' => \Illuminate\Support\Facades\Request::ip(), 
                    'agent' => \Illuminate\Support\Facades\Request::header('User-Agent'), 
                    'user_id' => auth('api')->id(),
                ]);
            break;
            case 'leave_request_confirm':
                Activity::create([
                    'subject' => auth('api')->user()->first_name.' '.auth('api')->user()->last_name. ($status ? ' has successfully ' : 'unsuccessfully ').'confirmed a leave request with ID: '.$item_id,
                    'url' => 'Confirm Leave Request',
                    'method' => 'Leave', 
                    'ip' => \Illuminate\Support\Facades\Request::ip(), 
                    'agent' => \Illuminate\Support\Facades\Request::header('User-Agent'), 
                    'user_id' => auth('api')->id(),
                ]);
                break;
            case 'leave_request_new':
                Activity::create([
                    'subject' => auth('api')->user()->first_name.' '.auth('api')->user()->last_name. ($status ? ' has successfully ' : 'unsuccessfully ').'applied for leave request'.($status ? 'with ID: '.$item_id : ''),
                    'url' => 'New Leave Request',
                    'method' => 'Leave', 
                    'ip' => \Illuminate\Support\Facades\Request::ip(), 
                    'agent' => \Illuminate\Support\Facades\Request::header('User-Agent'), 
                    'user_id' => auth('api')->id(),
                ]);
                break;
            case 'leave_request_reject':
                Activity::create([
                    'subject' => auth('api')->user()->first_name.' '.auth('api')->user()->last_name. ($status ? ' has successfully ' : 'unsuccessfully ').'rejected a leave request with ID: '.$item_id,
                    'url' => 'Reject Leave Request',
                    'method' => 'Leave', 
                    'ip' => \Illuminate\Support\Facades\Request::ip(), 
                    'agent' => \Illuminate\Support\Facades\Request::header('User-Agent'), 
                    'user_id' => auth('api')->id(),
                ]);
                break;
            case 'leave_type_create':
                Activity::create([
                    'subject' => auth('api')->user()->first_name.' '.auth('api')->user()->last_name. ($status ? ' has successfully created a leave type with ID: '.$item_id : 'unsuccessfully tried to create a leave type'),
                    'url' => 'Create Leave Request',
                    'method' => 'Leave Type', 
                    'ip' => \Illuminate\Support\Facades\Request::ip(), 
                    'agent' => \Illuminate\Support\Facades\Request::header('User-Agent'), 
                    'user_id' => auth('api')->id(),
                ]);
                break;
            case 'leave_type_delete':
                Activity::create([
                    'subject' => auth('api')->user()->first_name.' '.auth('api')->user()->last_name. ($status ? ' has successfully deleted' : 'unsuccessfully tried to delete').' a leave type with ID: '.$item_id,
                    'url' => 'Delete Leave Request',
                    'method' => 'Leave Type', 
                    'ip' => \Illuminate\Support\Facades\Request::ip(), 
                    'agent' => \Illuminate\Support\Facades\Request::header('User-Agent'), 
                    'user_id' => auth('api')->id(),
                ]);
            break;
            case 'leave_type_update':
                Activity::create([
                    'subject' => auth('api')->user()->first_name.' '.auth('api')->user()->last_name. ($status ? ' has successfully ' : 'unsuccessfully ').'updated a leave type with ID: '.$item_id,
                    'url' => 'Update Leave Request',
                    'method' => 'Leave Type', 
                    'ip' => \Illuminate\Support\Facades\Request::ip(), 
                    'agent' => \Illuminate\Support\Facades\Request::header('User-Agent'), 
                    'user_id' => auth('api')->id(),
                ]);
            break;
            case 'Store Item Batch Increase':
                Activity::create([
                    'subject' => 'Store Item Batch increase '.($status ? 'was successful. ': 'was unsuccesful. ').'Details of the transaction: <br />Store ID: '.$item_id['store_id'].'<br />Item ID: '.$item_id['item_id'].'<br />Quantity: '.$item_id['quantity'],
                    'url' => url()->current(),
                    'method' => 'Inventory Manager', 
                    'ip' => \Illuminate\Support\Facades\Request::ip(), 
                    'agent' => \Illuminate\Support\Facades\Request::header('User-Agent'), 
                    'user_id' => auth('api')->id() ?? Auth::id(),
                ]);
            break;
            case 'Store Item Batch Reduce':
                Activity::create([
                    'subject' => 'Store Item Batch reduce '.($status ? 'was successful. ': 'was unsuccesful. ').'Details of the transaction: <br />Store ID: '.$item_id['store_id'].'<br />Item ID: '.$item_id['item_id'].'<br />Transaction Type: '.$item_id['transaction'].'<br />Quantity: '.$item_id['quantity'],
                    'url' => url()->current(),
                    'method' => 'Inventory Manager', 
                    'ip' => \Illuminate\Support\Facades\Request::ip(), 
                    'agent' => \Illuminate\Support\Facades\Request::header('User-Agent'), 
                    'user_id' => auth('api')->id() ?? Auth::id(),
                ]);
            break;
            default:
                Activity::create([
                    'subject' => $type.($status ? ' was successful ID:'.$item_id : ' was unsuccessful ').' by '.auth('api')->user()->first_name.' '.auth('api')->user()->last_name,
                    'url' => url()->current(),
                    'method' => $type, 
                    'ip' => \Illuminate\Support\Facades\Request::ip(), 
                    'agent' => \Illuminate\Support\Facades\Request::header('User-Agent'), 
                    'user_id' => auth('api')->id(),
                ]);
        }
    }
}