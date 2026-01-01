<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\CRM\CustomerTrait;
use App\Http\Traits\Procurement\SettingsTrait;
use App\Http\Traits\Sales\OrderTrait;
use App\Mail\Sales\QuotationMail;
use App\Models\CRM\Customer;
use App\Models\Sales\Quotation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class QuotationController extends Controller
{
    use CustomerTrait, OrderTrait, SettingsTrait;

    public function destroy(string $id)
    {
        //
    }

    public function index()
    {
        if (isset($_GET['query'])){
            $quotations = $this->sales_quotation_get_all('search', ['status' => $_GET['status'] ?? 'all', 'search' => $_GET['search'] ], true, true, 30);
        }
        else{
            $quotations = $this->sales_quotation_get_all('status', $_GET['status'] ?? 'all', true, true, 30);
        }

        return response()->json([
            'quotations' => $quotations,
        ]);
    }

    public function mail(string $id)
    {
        try{
            // 1) Fetch invoice & customer
            $quotation  = Quotation::with('quotation_items')->findOrFail($id);

            $quotation->status = 'sent';
            $quotation->updated_by = Auth::id() ?? auth('api')->id(); // Update status to sent
            $quotation->save(); // Save the updated status

            if ($quotation->customer_id == 0){
                //return response()->json(['error' => 'Quotation has no associated customer.'], 400);
            }
            else{
                $customer = Customer::findOrFail($quotation->customer_id);
                if (!$customer) {
                    //return response()->json(['error' => 'Customer not found.'], 404);
                }
                else if(!$customer->email) {
                    //return response()->json(['error' => 'Customer email not found.'], 400);
                }
                
                //Mail::to($customer->email)->send(new QuotationMail($customer, $quotation));
            }
            
            return response()->json(['message' => 'Quotation emailed successfully.']);
        } 
        catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send email: ' . $e->getMessage()], 500);
        }
    }


    public function show(string $id)
    {
        $quotation = $this->sales_quotation_get_by('unique_id', $id, true);

        return response()->json([
            'quotation' => $quotation,
        ], is_string($quotation) ? 404 : 200);
    }

    public function store(Request $request)
    {
        $quotation = $this->sales_quotation_create($request);

        return response()->json([
            'quotation' => $quotation,
        ], is_string($quotation) ? 500 : 201);
    }

    public function update(Request $request, string $id)
    {
        $quotation = $this->sales_quotation_update($request, $id);
        
        return response()->json([
            'quotation' => $quotation,
        ], is_string($quotation) ? 500 : 200);
    }

}
