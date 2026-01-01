<?php

namespace App\Http\Controllers\Api\Loans;

use App\Http\Controllers\Controller;
use App\Http\Traits\LoanAccountTrait;
use App\Models\Finance\Bureau;
use App\Models\Finance\BureauProduct;
use App\Models\Loans\Account;
use App\Models\Loans\CreditScore;
use Illuminate\Http\Request;

class CreditScoreController extends Controller
{
    use LoanAccountTrait;
    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        return response()->json([
            'account' => Account ::where('id', '=', $request->input('loan_id'))->with(['repayments', 'user'])->first(),
            'bureaus' => Bureau ::select('id', 'name', 'link')->orderBy('name', 'ASC')->get(),
            'bureau_products' => BureauProduct ::orderBy('name', 'ASC')->get(),
            'credit_score' => $this->account_create_credit_score($request),
            'credit_scores' => CreditScore ::where('loan_id', '=', $request->input('loan_id'))->with('product.bureau', 'creator')->latest()->paginate(4),
        ]);
        
    }

    public function show($id)
    {
        return response()->json([
            'account' => Account ::where('id', '=', $id)->with(['repayments', 'user'])->first(),
            'bureaus' => Bureau ::select('id', 'name', 'link')->orderBy('name', 'ASC')->get(),
            'bureau_products' => BureauProduct ::orderBy('name', 'ASC')->get(),
            'credit_scores' => CreditScore ::where('loan_id', '=', $id)->with('product.bureau', 'creator')->latest()->paginate(4),
        ]);
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
