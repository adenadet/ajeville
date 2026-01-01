<?php

namespace App\Http\Controllers\Api\CRM;

use App\Http\Controllers\Controller;
use App\Http\Traits\CRM\CustomerTrait;
use App\Http\Traits\Finance\ExpenseTrait;
use App\Http\Traits\Finance\IncomeTrait;
use App\Http\Traits\Sales\OrderTrait;
use App\Imports\CRM\CustomerImport;
use Illuminate\Http\Request;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    use CustomerTrait, IncomeTrait, ExpenseTrait;
    public function destroy(string $id)
    {
        $customer = $this->crm_customer_delete($id);
        return response()->json([
            'customer' => $customer,       
        ], is_string($customer) ? 500 : 200);
    }
    
    public function import(Request $request)
    {
        $dent = explode("base64,", $request->input('file'));
        $decodedData = base64_decode($dent[1], true);
        if ($decodedData === false) {
            return response()->json([
                'result' => null,
                'message' => 'The provided string is not valid Base64.',
            ], 500);
        }
        
        $fileSignature = substr($decodedData, 0, 4);
        $validCsvSignature = chr(0xEF) . chr(0xBB) . chr(0xBF); // Optional BOM for UTF-8 CSV
        $validXlsxSignature = chr(0x50) . chr(0x4B) . chr(0x03) . chr(0x04); // XLSX files (PKZIP format)

        if ($fileSignature === $validCsvSignature || strpos($decodedData, ',') !== false) {
            $fileType = "xlsx";
        } 
        elseif ($fileSignature === $validXlsxSignature) {
            $fileType = "xlsx";
        }
        else {
            return response()->json([
                'result' => null,
                'message' => "The Base64 string does not represent a valid CSV or Excel file."
            ]);
        }

        $fileName = 'uploaded_file_'.time().'.'. $fileType;
        $tempPath = public_path('uploads/files/' . $fileName);
        file_put_contents($tempPath, $decodedData);

        try {
            $query = Excel::import(new CustomerImport, $tempPath);
            @unlink($tempPath);
            return response()->json([
                'result' => $query,
                'message' => 'The file was imported successfully',
            ]);
        }

        catch(Exception $e){
            @unlink($tempPath);
            return response()->json(['error' => 'Failed to process the file', 'details' => $e->getMessage()], 500);
        }
    }

    public function index()
    {
        $customers = (isset($_GET['query']) && $_GET['query'] != '')
                        ? $this->crm_customer_get_all('quick_search', $_GET['query'], true, true, $_GET['page'] ?? 1)
                        : $this->crm_customer_get_all('active', null, true, true, $_GET['page'] ?? 1);
        return response()->json(['customers' =>  $customers], is_string($customers) ? 500 : 200);
    }

    public function initials()
    {
        return response()->json([
            'categories' => $this->crm_customer_category_get_all('active', null, false, false, null),  
            'customers' =>  $this->crm_customer_get_all('active', null, false, false, null),   
        ]);
    }

    public function show(string $id)
    {
        $customer = $this->crm_customer_get_by('id', $id, true);
        return response()->json([
            'customer' => $customer,
            'contacts' => $this->crm_customer_contact_get_all('customer', $id, true, false, null),
            //'orders' => $this->_get_all('customer', $id, true, false, null),
            'transactions' => $this->finance_main_transaction_get_all(null, ['customer_id' => $id], true, true)
        ], is_string($customer) ? 404 : 200);
    }

    public function store(Request $request)
    {
        $customer = $this->crm_customer_create($request);
        
        return response()->json([
            'customer' => $customer, 
        ], is_string($customer) ? 500 : 201);
    }

    public function update(Request $request, string $id)
    {
        $customer = $this->crm_customer_update($request, $id);
        return response()->json([
            'customer' => $customer,       
        ], is_string($customer) ? 501 : 200);
    }

    public function upload(Request $request, string $id)
    {
        $request->validate([
            'csv' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $importer = new CustomerImport;
        Excel::import($importer, $request->file('csv'));

        return response()->json([
            'message' => "Imported {$importer->inserted} customers. Skipped {$importer->skipped} duplicates.",
        ]);
    }

}
