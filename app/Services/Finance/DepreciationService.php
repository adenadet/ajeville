<?php
namespace App\Services\Finance;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DepreciationService
{
    protected $assetTable = 'assets';

    public function run(?string $asOf = null)
    {
        $asOf = $asOf ? Carbon::parse($asOf) : Carbon::now();

        // load assets
        $assets = DB::table($this->assetTable)
            ->select('id','name','asset_code','purchase_date','cost','useful_life_years','salvage_value','depreciation_method')
            ->get();

        $lines = [];
        $totalCost = 0; $totalAccumulated = 0; $totalBook = 0;

        foreach ($assets as $a) {
            $purchase = Carbon::parse($a->purchase_date);
            $yearsPassed = $purchase->diffInDays($asOf) / 365.0;
            $cost = (float)$a->cost;
            $salvage = (float)($a->salvage_value ?? 0);
            $life = max(1, (float)$a->useful_life_years);
            $method = $a->depreciation_method ?? 'straight';

            if ($method === 'straight') {
                $annual = ($cost - $salvage) / $life;
                $accumulated = min($annual * $yearsPassed, $cost - $salvage);
            } elseif ($method === 'declining') {
                // simple double-declining example
                $rate = (2 / $life);
                $book = $cost;
                $accum = 0;
                $years = floor($yearsPassed);
                for ($y=0;$y<$years;$y++) {
                    $dep = $book * $rate;
                    $dep = min($dep, $book - $salvage);
                    $accum += $dep;
                    $book -= $dep;
                }
                // prorate current year
                $partial = $yearsPassed - $years;
                if ($partial > 0) {
                    $dep = $book * $rate * $partial;
                    $dep = min($dep, $book - $salvage);
                    $accum += $dep;
                }
                $accumulated = $accum;
            } else {
                $annual = ($cost - $salvage) / $life;
                $accumulated = min($annual * $yearsPassed, $cost - $salvage);
            }

            $bookValue = $cost - $accumulated;
            $lines[] = [
                'id' => $a->id,
                'asset_code' => $a->asset_code,
                'name' => $a->name,
                'purchase_date' => $a->purchase_date,
                'cost' => round($cost,2),
                'accumulated_depreciation' => round($accumulated,2),
                'book_value' => round($bookValue,2),
                'method' => $method,
            ];

            $totalCost += $cost;
            $totalAccumulated += $accumulated;
            $totalBook += $bookValue;
        }

        return [
            'as_of' => $asOf->toDateString(),
            'lines' => $lines,
            'totals' => [
                'cost' => round($totalCost,2),
                'accumulated_depreciation' => round($totalAccumulated,2),
                'book_value' => round($totalBook,2),
            ]
        ];
    }
}
