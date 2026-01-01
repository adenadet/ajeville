<?php

namespace App\Imports\Inventory;

use App\Http\Traits\Inventory\ItemTrait;
use App\Models\Inventory\Item;
use App\Models\Inventory\Classification;
use App\Models\Inventory\Category;
use App\Models\Inventory\Brand;
use App\Models\Inventory\ItemType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ItemImport implements  ToCollection, WithHeadingRow
{
    use ItemTrait;
    protected $previewMode;
    protected $log = [];

    public function __construct($previewMode = false)
    {
        $this->previewMode = $previewMode;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            // Skip if required fields are missing
            if (!$row['name']) {
                $this->log[] = [
                    'row' => $index + 2,
                    'status' => 'skipped',
                    'reason' => 'Missing name or unique_id',
                    'data' => $row
                ];
                continue;
            }

            $brand_id = $category_id = $classification_id = $type_id =null;
            
            if (isset($row['brand'])){
                $brand = Brand::firstOrCreate(['name' => $row['brand']]);
                $brand_id = $brand->id();
            }

            if (isset($row['category'])){
                $category = Category::firstOrCreate(['name' => $row['category']]);
                $category_id = $category->id();
            }

            if (isset($row['classification'])){
                $classification = Classification::firstOrCreate(['name' => $row['classification']]);
                $classification_id = $classification->id();
            }

            if (isset($row['type'])){
                $type = ItemType::firstOrCreate(['name' => $row['type']]);
                $type_id = $type->id();
            }
            
            // Check for duplicates by unique_id or barcode
            $item = Item::where('barcode', $row['barcode'])->first();

            if ($item) {
                // Update logic
                if (!$this->previewMode) {
                    $item->update([
                        'average_landing_cost' => $row['landing_cost'] ?? 0.00,
                        'billable' => $row['billable'] ?? 1,
                        'barcode' => $row['barcode'] ?? null,
                        'brand_id' => $brand_id,
                        'category_id' => $category_id,
                        'classification_id' => $classification_id,
                        'consumable' => $row['consumable'] ?? 1,
                        'description' => $row['description'],
                        'is_package' => $row['is_package'] ?? false,
                        'last_landing_cost' => $row['landing_cost'] ?? 0.00,
                        'name' => $row['name'],
                        'specific_id' => $row['specific_id'] ?? null,
                        'status' => $row['status'] ?? 1,
                        'type_id' => $type_id,
                        'unique_id' => $row['unique_id'] ?? $this->inventory_generate_unique_id($type) ,
                        'created_by' => Auth::id() ?? auth('api')->id(),
                        'updated_by' => Auth::id() ?? auth('api')->id(),
                    ]);
                }

                $this->log[] = [
                    'row' => $index + 2,
                    'status' => 'updated',
                    'reason' => 'Existing item found',
                    'data' => $row
                ];
            } else {
                if (!$this->previewMode) {
                    Item::create([
                        'name' => $row['name'],
                        'unique_id' => $row['unique_id'],
                        'specific_id' => $row['barcode'],
                        'barcode' => $row['barcode'],
                        'type_id' => $type->id,
                        'classification_id' => $classification->id,
                        'category_id' => $category->id,
                        'brand_id' => $brand->id ?? null,
                        'last_landing_cost' => $row['last_landing_cost'],
                        'average_landing_cost' => $row['average_landing_cost'],
                        'description' => $row['description'],
                        'status' => 'active',
                        'image' => null,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);
                }

                $this->log[] = [
                    'row' => $index + 2,
                    'status' => 'created',
                    'reason' => 'New item',
                    'data' => $row
                ];
            }
        }
    }

    public function getLog()
    {
        return $this->log;
    }
}

