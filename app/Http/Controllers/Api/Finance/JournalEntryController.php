<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\JournalEntry;
use Illuminate\Http\Request;

class JournalEntryController extends Controller
{
    public function index()
    {
        return JournalEntry::with('lines.account')->latest()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'description' => 'required',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.debit' => 'numeric',
            'lines.*.credit' => 'numeric',
        ]);

        $totalDebit = collect($data['lines'])->sum('debit');
        $totalCredit = collect($data['lines'])->sum('credit');

        if ($totalDebit !== $totalCredit) {
            return response()->json(['error' => 'Debits and Credits must balance'], 422);
        }

        $entry = JournalEntry::create([
            'date' => $data['date'],
            'description' => $data['description'],
        ]);

        foreach ($data['lines'] as $line) {
            $entry->lines()->create($line);
        }

        return $entry->load('lines.account');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
