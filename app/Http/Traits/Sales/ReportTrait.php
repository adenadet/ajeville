<?php
namespace App\Http\Traits\Sales;

use App\Http\Traits\Finance\IncomeTrait;
use App\Http\Traits\Finance\MainTransactionTrait;
use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Http\Traits\Inventory\StoreTrait;
use App\Models\CRM\Customer;
use App\Models\Finance\Income;
use App\Models\Inventory\OrderFulfillment;
use App\Models\Inventory\StoreItem;
use App\Models\Inventory\StoreItemBatch;
use App\Models\Procurement\Batch;
use App\Models\Procurement\PaymentTerm;
use App\Models\Sales\DeliveryNote;
use App\Models\Sales\DeliveryNoteItem;
use App\Models\Sales\Order;
use App\Models\Sales\OrderApproval;
use App\Models\Sales\OrderItem;
use App\Models\Sales\OrderReturn;
use App\Models\Sales\OrderReturnItem;
use App\Models\Sales\Quotation;
use App\Models\Sales\QuotationItem;
use App\Services\Finance\OrderIncomeService;
use App\Services\Inventory\IssuanceService;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


trait ReportTrait {

    public function sales_report_daily_user_sales($startDate, $endDate, $users = [])
    {
        $quest = Order::query()
            ->join('sales_order_items as oi', 'oi.so_id', '=', 'sales_orders.unique_id')
            ->select([ 'sales_orders.id as order_id', 'sales_orders.unique_id as order_unique_id', 'sales_orders.created_by',
                // Sales Value
                DB::raw('SUM((oi.quantity * oi.unit_price) - COALESCE(oi.discount, 0)) as sales_value'),
                // Discounts
                DB::raw('SUM(COALESCE(oi.discount, 0)) as item_discount'),
                DB::raw('COALESCE(sales_orders.discount, 0) as order_discount'),
                // Logistics
                DB::raw('COALESCE(sales_orders.logistics, 0) as logistics'),
            ])
            ->whereBetween('sales_orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59',]);
        //$quest =  !is_null($users) && empty($users) && is_array($users) ? $quest->whereIn('sales_orders.created_by', $users) : $quest;
            
        $orderTotalsSubQuery = $quest->groupBy('sales_orders.id', 'sales_orders.unique_id', 'sales_orders.created_by', 'sales_orders.discount', 'sales_orders.logistics');
        //echo $orderTotalsSubQuery->count();
        return DB::query()
            ->fromSub($orderTotalsSubQuery, 'ot')
            ->select([
                'ot.created_by',
                'users.first_name',
                'users.last_name',
                // Transactions
                DB::raw('COUNT(DISTINCT ot.order_id) as transactions_count'),
                // Discounts
                DB::raw('ROUND(SUM(ot.item_discount + ot.order_discount), 2) as total_discounts'),
                // Sales
                DB::raw('ROUND(SUM(ot.sales_value), 2) as total_sales'),
                // Tax
                DB::raw('ROUND(SUM(0.075 * ot.sales_value), 2) as total_tax'),
                // Logistics
                DB::raw('ROUND(SUM(ot.logistics), 2) as total_logistics'),
                // Net Sales
                DB::raw('ROUND(SUM(ot.sales_value + ot.logistics + (0.075 * ot.sales_value)- (ot.item_discount + ot.order_discount)), 2) as net_sales'),
            ])
            ->groupBy('ot.created_by')
            ->leftJoin('users', 'ot.created_by', '=', 'users.id')->get();
    }

    public function sales_report_item_detailed($startDate, $endDate, $filters = [])
    {
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate   = Carbon::parse($endDate)->endOfDay();

        $query = DB::table('sales_order_items as soi')
            ->join('sales_orders as so', 'so.unique_id', '=', 'soi.so_id')
            ->join('inventory_items as i', 'i.id', '=', 'soi.item_id')
            ->leftJoin('inventory_item_categories as c', 'c.id', '=', 'i.category_id')
            ->whereBetween('so.date', [$startDate, $endDate])
            ->whereNull('so.deleted_at')
            ->whereNull('soi.deleted_at')
            ->select(['i.id as item_id', 'i.name as item_name', 'i.unique_id', 'c.name as category',

                DB::raw('SUM(soi.quantity) as quantity_sold'),
                DB::raw('COUNT(DISTINCT so.id) as total_orders'),

                DB::raw('AVG(soi.unit_price) as avg_unit_price'),
                DB::raw('SUM(soi.quantity * soi.unit_price) as gross_sales'),

                DB::raw('SUM(soi.discount) as total_discount'),
                DB::raw('SUM((soi.quantity * soi.unit_price) - soi.discount) as net_sales'),

                DB::raw('AVG(COALESCE(NULLIF(i.average_landing_cost, 0), 0)) as avg_cost_price'),
                DB::raw('SUM(soi.quantity * COALESCE(NULLIF(i.average_landing_cost, 0), 0)) as total_cost'),

                DB::raw('SUM(
                    ((soi.quantity * soi.unit_price) - soi.discount) 
                    - (soi.quantity *  COALESCE(NULLIF(i.average_landing_cost, 0), 0))
                ) as gross_profit')
            ])
            ->groupBy('i.id','i.name','i.unique_id','c.name');
        
        if (!empty($filters['branch_id'])) {
            $query->where('so.branch_id', $filters['branch_id']);
        }

        if (!empty($filters['item_id'])) {
            $query->where('i.id', $filters['item_id']);
        }
        
        return $query->orderByDesc('net_sales')->get();
    }


    public function sales_report_tax_sales_vat($start_date, $end_date){

        //$startDate = Carbon::parse($request->start_date)->startOfDay();
        //$endDate   = Carbon::parse($request->end_date)->endOfDay();

        $orders = Order::query()->whereBetween('date', [$start_date, $end_date])
                ->where('payment_status', '=', Order::PaymentStatusPaid)
                ->whereNull('deleted_at');

        $summary = [
            'total_taxable_sales' => 0,
            'total_vat_collected' => 0,
            'transaction_count'  => $orders->count(),
        ];
        $taxable_orders = [];
        $vatByDay = [];
        $vatByBranch = [];
        
        foreach ($orders as $order) {

            $itemTotal = $order->order_items->sum(function ($item) {
                return ((float) $item->unit_price * (int) $item->quantity) - ((float) $item->discount ?? 0);
            });
            $orderDiscount = (float) $order->discount ?? 0;
            $taxableAmount = max($itemTotal - $orderDiscount, 0);
            $vatAmount = $taxableAmount * 0.075;

            $summary['total_taxable_sales'] += $itemTotal;
            $summary['total_vat_collected'] += $vatAmount;

            /** VAT BY DAY */
            $day = $order->date->format('Y-m-d');
            $vatByDay[$day] = ($vatByDay[$day] ?? 0) + $vatAmount;

            /** VAT BY BRANCH */
            $branchId = $order->branch_id;
            $vatByBranch[$branchId] = ($vatByBranch[$branchId] ?? 0) + $vatAmount;

            $taxable_orders[] = [
                'date' => $order->date,
                'unique_id' => $order->unique_id,
                'order_value' => $taxableAmount,
                'vat_amount' => $vatAmount,
            ];
        }

        return [
            'summary' => $summary,
            'orders' => $taxable_orders,
            'vat_by_day' => $vatByDay,
            'vat_by_branch' => $vatByBranch,
        ];            
    }

}
