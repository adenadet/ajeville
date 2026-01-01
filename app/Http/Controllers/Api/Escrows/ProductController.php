<?php

namespace App\Http\Controllers\Api\Escrows;

use App\Http\Controllers\Controller;
use App\Http\Traits\Escrows\PaymentTrait;
use App\Http\Traits\Escrows\ProductTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class ProductController extends Controller
{
    use PaymentTrait, ProductTrait;
   
    public function generate_report(Request $request)
    {
        $products = $this->escrow_products_all($_GET['t'] ?? 'mine', null, true, false, null);
        // Create unique filename
        $filename = 'product_list_' . now()->format('Ymd_His') . '.csv';

        // Define CSV headers
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        // Open output stream
        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');

            // Column headings
            fputcsv($file, [
                'Product Name',
                'Product Code',
                'Description',
                'Role',
                'Created At',
            ]);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->description,
                    $product->item_code,
                    $product->details,
                    ucfirst($product->role),
                    optional($product->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function index()
    {
        return response()->json([
            'products' => $this->escrow_products_all($_GET['t'], null, true, true, $_GET['page']), 
        ]);
    }

    public function store(Request $request)
    {
        return response()->json([
            'product' => $this->escrow_products_create($request),
        ]);
    }

    public function show(string $id)
    {
        return response()->json([
            'product' => $this->escrow_products_get_by_id($id),
        ]);
    }

    public function update(Request $request, string $id)
    {
        return response()->json([
            'product' => $this->escrow_products_update($request, $id),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product =$this->escrow_products_deactivate( $id);
        return response()->json([
            'product' => $product,
        ], is_string($product)? 500 : 200);
    }
}
