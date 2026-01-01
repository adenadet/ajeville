<?php
namespace App\Http\Traits\Escrows;

use App\Http\Traits\General\FileManagerTrait;
use App\Models\Escrows\ItemType;
use App\Models\Escrows\Product;
use App\Models\Escrows\ProductImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
trait ProductTrait {
    use FileManagerTrait;

    public function product_generate_item_code(){
        $code = 'PRD'.strtoupper(dechex(time())).random_int(10, 99);
        $product = Product::where('item_code', '=', $code)->first();
        if($product){
            return $this->product_generate_item_code();
        }else{
            return $code;
        }
    }

    public function escrow_products_all($type, $specific, $detailed, $paginated, $page){
        switch ($type){
            case 'all':
                $query = Product::whereNull('deleted_at');
            break;
            case 'my':
                $query = Product::where('owner_id', '=', Auth::id() ?? auth('api')->id());
            break;
            default:
                $query = Product::whereNull('deleted_at');
        }

        $query = $detailed ? $query->with(['images', 'owner', 'transactions']) : $query->select('id', 'amount', 'item_code');
        $query = $paginated ? $query->paginate(12) : $query->get();

        return $query;
    }

    public function escrow_products_create($data){
        $product = Product::create([
            'owner_id' => $data['owner_id'] ?? (Auth::id() ?? auth('api')->id()),
            'category_id' => $data['category_id'], 
            'image' => (!isset($data['image'])) || is_null($data['image']) ? 'img/products/default.png' : $this->file_upload($data['image'], 'image', 'img/products', 12, true),
            'item_code' => $data['item_code'] ?? $this->product_generate_item_code(),
            'description' => $data['description'],
            'details' => $data['details'],
            'unit_price' => $data['unit_price'],
            'status' => $data['status'] ?? 1,
            'quantity' => $data['quantity'] ?? NULL,
            'created_by' => Auth::id() ?? auth('api')->id(),
            'updated_by' => Auth::id() ?? auth('api')->id(),
        ]);

        return $product;
    }
    public function escrow_products_search($data){}

    public function escrow_products_get_by_id($id, $detailed = true){
        //$product = Product::findOrFail($id);
        return  Product::where('id', '=', $id)->orWhere('item_code', '=', $id)->with(['category', 'owner.company', 'transactions', 'images'])->first();
    }

    public function escrow_products_transactions($data, $transaction_id){}

    
    public function escrow_products_update($data, $id){
        DB::beginTransaction();

        try{
            $product = Product::findOrFail($id);
            $product->owner_id = $data['owner_id'] ?? (Auth::id() ?? auth('api')->id());
            $product->category_id = $data['category_id'];
            $product->item_code = $data['item_code'] ?? $this->product_generate_item_code();
            $product->description = $data['description'];
            $product->details = $data['details'];
            $product->unit_price = $data['unit_price'];
            $product->status = $data['status'] ?? 1;
            $product->quantity = $data['quantity'] ?? NULL;
            $product->updated_by = Auth::id() ?? auth('api')->id();
            
            $product->save();

            DB::commit();
            return $product;
        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json(['error' => 'An error occurred while updating the product.'], 500);
        }
    }

    public function escrow_item_type_create($data){}

    public function escrow_item_type_delete($data){}

    public function escrow_item_type_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'all':
                $query = ItemType::withTrashed();
            break;
            case 'active':
                $query = ItemType::where('status', '=', 1);
            break;
            case 'inactive':
                $query = ItemType::where('status', '!=', 1);
            break;
        }

        $query = $query->orderBy('name', 'ASC');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function escrow_item_type_get_by($type, $specific, $detailed){
        
    }

    public function escrow_item_type_update($data, $id){}
}