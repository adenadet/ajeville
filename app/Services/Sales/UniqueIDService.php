<?php 
namespace App\Services\Sales;

use App\Models\Inventory\OrderFulfillment;
use App\Models\Sales\DeliveryNote;
use App\Models\Sales\Order;
use App\Models\Sales\OrderItem;
use App\Models\Sales\OrderReturn;
use App\Models\Sales\OrderReturnItem;
use App\Models\Sales\Quotation;
use App\Services\Sales\OrderValidationService;
use Illuminate\Support\Facades\DB;

class UniqueIDService
{

    private function sales_generateRandomString($length = 10){
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function sales_generate_unique_id($type, $length = 10){
    
        $code = $this->sales_generateRandomString($length);
        switch($type){
            case 'deliverable':
                $prefix = 'DEL';
                $query = DeliveryNote::where('uuid', '=', $prefix.'-'.$code)->first();
                if($query){
                    return $this->sales_generate_unique_id('deliverable');
                }
                else{
                    return $prefix.'-'.$code;
                }
            case 'fulfillment':
                $prefix = 'FFL';
                $query = OrderFulfillment::where('uuid', '=', $prefix.'-'.$code)->first();
                if($query){
                    return $this->sales_generate_unique_id('fulfillment');
                }
                else{
                    return $prefix.'-'.$code;
                }
            case 'order':
                $prefix = 'ORD';   
                $query = Order::where('unique_id', '=', $prefix.'-'.$code)->first();
                if($query){
                    return $this->sales_generate_unique_id('order');
                }else{
                    return $prefix.'-'.$code;
                }
            case 'order_item':
                $prefix = 'OIT';   
                $query = OrderItem::where('uuid', '=', $prefix.'-'.$code)->first();
                if($query){
                    return $this->sales_generate_unique_id('order_item');
                }else{
                    return $prefix.'-'.$code;
                }
            case 'quotation':
                $prefix = 'QUT';
                $query = Quotation::where('uuid', '=', $prefix.'-'.$code)->first();
                if($query){
                    return $this->sales_generate_unique_id('quotation');
                }else{
                    return $prefix.'-'.$code;
                }
            case 'return':
                $prefix = 'RTN';   
                $query = OrderReturn::where('unique_id', '=', $prefix.'-'.$code)->first();
                if($query){
                    return $this->sales_generate_unique_id('return');
                }else{
                    return $code;
                }
            case 'return_item':
                $prefix = 'RTI';
                $query = OrderReturnItem::where('uuid', '=', $prefix.'-'.$code)->first();
                if($query){
                    return $this->sales_generate_unique_id('return_item');
                }else{
                    return $code;
                }   
        }
    }
}