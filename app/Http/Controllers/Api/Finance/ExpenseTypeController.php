<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Traits\Finance\ExpenseTrait;
class ExpenseTypeController extends Controller
{
    use ExpenseTrait;
    
    public function destroy(string $id)
    {
        
        $expense_type = $this->finance_expense_type_deactivate($id);

        return response()->json([
            'expense_type' => $expense_type,
        ], is_string($expense_type) ? 500 : 201);
    }
    
    public function index()
    {
        return response()->json([
            'expense_types' => $this->finance_expense_type_get_all($_GET['type'] ?? 'active', $_GET['query'] ?? null, true, true, null),
        ]);
    }

    public function show(string $id)
    {
        return response()->json([
            'expense_type' => $this->finance_expense_type_get_by(null, $id, true),
        ]);
    }

    public function store(Request $request)
    {
        $expense_type = $this->finance_expense_type_create($request);

        return response()->json([
            'expense_type' => $expense_type,
        ], is_string($expense_type) ? 500 : 201);
    }
    public function update(Request $request, string $id)
    {
        
        $expense_type = $this->finance_expense_type_update($request, $id);

        return response()->json([
            'expense_type' => $expense_type,
        ], is_string($expense_type) ? 500 : 201);
    }
}
