<?php

namespace App\Http\Controllers\Api\Equipments;

use App\Http\Controllers\Controller;
use App\Http\Traits\Equipments\AssetTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

class AssetController extends Controller
{
    use AssetTrait;
    public function assign($request, $id)
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:locations,id',
            'assigned_to_user_id' => 'required|exists:users,id',
        ]);
        
        $assignment = $this->equipment_asset_assign($validated, $id);
        
        return response()->json([
            'assignment' => $assignment
        ], is_string($assignment) ? 500 : 200);
    }
    public function index()
    {
        $assets = $this->equipment_asset_get_all($_GET['type'] ?? 'all', $_GET, true, true, $_GET['page'] ?? 1);
        
        return response()->json([
            'assets' => $assets
        ], is_string($assets) ? 500 : 200);
    }

    public function initials()
    {
        return response()->json([
            'asset_types' => $this->equipment_asset_type_get_all('active', null, false, false, null),
        ]);
    }

    public function report(){
        $assets = $this->equipment_asset_get_all($_GET['type'] ?? 'all', $_GET, true, true, $_GET['page'] ?? 1);
    
        
        $filename = 'all_asset_' . now()->format('Ymd_His') . '.csv';
        // Define CSV headers
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        // Open output stream
        $callback = function () use ($assets) {
            $file = fopen('php://output', 'w');

            // Column headings
            fputcsv($file, [
                'Asset Name',
                'Category',
                'Assigned To',
                'Location',
                'Purchase Value',
                'Purchase Date',
                'Depreciation Rate (%)',
                'Accumulated Depreciation',
                'Current Value',
                'Status',
            ]);

            foreach ($assets as $asset) {
                $categoryName = $asset->category ? $asset->category->name : 'Not Assigned';
                $assignedTo = $asset->assignedUser ? trim(($asset->assignedUser->last_name ?? '') . ' ' . ($asset->assignedUser->first_name ?? '')) : 'Not Assigned';
                $locationName = $asset->location ? $asset->location->name : 'No Location Assigned';

                // Numeric values
                $purchaseValue = is_numeric($asset->purchase_value) ? floatval($asset->purchase_value) : 0.0;
                $depreciationRatePercent = is_numeric($asset->depreciation_rate) ? floatval($asset->depreciation_rate) : 0.0;

                // Calculate elapsed years (fractional) from acquisition_date to today
                $acqDate = $asset->acquisition_date ? Carbon::parse($asset->acquisition_date) : null;
                $yearsElapsed = 0.0;
                if ($acqDate) {
                    // use days / 365.25 for fractional years (accounts for leap years)
                    $days = $acqDate->diffInDays(Carbon::now());
                    $yearsElapsed = $days / 365.25;
                }

                // Straight-line accumulated depreciation = purchase_value * (rate_decimal) * yearsElapsed
                $rateDecimal = $depreciationRatePercent / 100.0;
                $accumulatedDepreciation = $purchaseValue * $rateDecimal * $yearsElapsed;

                // Current value = purchase - accumulated, never below 0
                $currentValue = max(0, $purchaseValue - $accumulatedDepreciation);

                // Round / format as you like
                $purchaseValueFormatted = number_format($purchaseValue, 2, '.', '');
                $accumulatedDepreciationFormatted = number_format($accumulatedDepreciation, 2, '.', '');
                $currentValueFormatted = number_format($currentValue, 2, '.', '');
                $depreciationRateFormatted = number_format($depreciationRatePercent, 2, '.', '');

                // Status - adapt as needed
                $status = $asset->status;

                fputcsv($file, [
                    $asset->name,
                    $categoryName,
                    $assignedTo,
                    $locationName,
                    $purchaseValueFormatted,
                    $asset->acquisition_date,
                    $depreciationRateFormatted,
                    $accumulatedDepreciationFormatted,
                    $currentValueFormatted,
                    $status,
                ]);
            }

            fclose($file);
        };
        
        return Response::stream($callback, 200, $headers);
    }

    public function return($id)
    {
        $assignment = $this->equipment_asset_return($id);
        
        return response()->json([
            'assignment' => $assignment
        ], is_string($assignment) ? 500 : 200);
    }
    
    public function show(string $id)
    {
        $asset = $this->equipment_asset_get_by('uuid', $id, true);
        
        return response()->json([
            'asset' => $asset
        ], is_string($asset) ? 500 : 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type_id' => 'required|exists:equipment_asset_types,id',
            'location_id' => 'nullable',
            'assigned_to_user_id' => 'nullable|exists:users,id',
        ]);

        $asset = $this->equipment_asset_create($request);
        
        return response()->json([
            'asset' => $asset
        ], is_string($asset) ? 500 : 200);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'type_id' => 'sometimes|exists:equipment_asset_types,id',
            'location_id' => 'sometimes|exists:locations,id',
            'assigned_to_user_id' => 'sometimes|exists:users,id',
        ]);

        $asset = $this->equipment_asset_update($request, $id);
        
        return response()->json([
            'asset' => $asset
        ], is_string($asset) ? 500 : 200);
    }

    public function destroy(string $id)
    {
        $asset = $this->equipment_asset_deactivate($id);
        
        return response()->json([
            'asset' => $asset
        ], is_string($asset) ? 500 : 200);
    }
}
