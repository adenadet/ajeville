<?php

namespace App\Models\Finance;

use App\Models\Structure;
use App\Models\Ticket\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Structure
{
    use HasFactory;

    const StatusIssued = 1;
    const StatusConfirmed = 2;
    const StatusPaid = 10;
    const StatusPartPaid = 5;
    const StatusRejected = 100;

    protected $primaryKey = 'id';

    protected $table = 'finance_invoices';
    protected $fillable = ['unique_id', 'invoice_number', 'classification_id', 'vendor_id', 'invoice_file', 'branch_id', 'date', 'due_date', 'amount', 'description', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'];

    public function branch(){
        return $this->belongsTo('App\Models\Operations\Branch', 'branch_id', 'id');
    }

    public function classification(){
        return $this->belongsTo('App\Models\Finance\ExpenseType', 'classification_id', 'id');
    }

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function expense(){
        return $this->morphOne('App\Models\Finance\Expense', 'expenseable', 'expenseable_type', 'expenseable_id');
    }

    public function expense_detail()
    {
        return $this->morphOne('App\Models\Finance\Expense', 'expenseable');
    }


    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }

    public function vendor(){
        return $this->belongsTo('App\Models\Procurement\Vendor', 'vendor_id', 'id');
    }

}