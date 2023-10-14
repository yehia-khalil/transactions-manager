<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $results = DB::table('transactions')
            ->select(
                DB::raw('MONTH(transactions.due_date) as month'),
                DB::raw('YEAR(transactions.due_date) as year'),
                DB::raw('SUM(
            CASE
                WHEN transactions.due_date < NOW() THEN
                    GREATEST(
                        CASE
                            WHEN is_vat_inclusive = 1 THEN transactions.amount * (1 + transactions.vat / 100)
                            ELSE transactions.amount
                        END -
                        COALESCE(transaction_payments.amount, 0),
                        0
                    )
                ELSE 0
            END
        ) as overdue'),
                DB::raw('SUM(
            CASE
                WHEN transactions.due_date >= NOW() THEN
                    GREATEST(
                        CASE
                            WHEN is_vat_inclusive = 1 THEN transactions.amount * (1 + transactions.vat / 100)
                            ELSE transactions.amount
                        END -
                        COALESCE(transaction_payments.amount, 0),
                        0
                    )
                ELSE 0
            END
        ) as outstanding'),
                DB::raw('SUM(
            COALESCE(transaction_payments.amount, 0)
        ) as paid')
            )
            ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
            ->whereBetween('transactions.due_date', [$request->startDate, $request->endDate])
            ->groupBy(DB::raw('MONTH(transactions.due_date)'), DB::raw('YEAR(transactions.due_date)'))
            ->get()
            ->toArray();


        return response()->json($results);
    }
}
