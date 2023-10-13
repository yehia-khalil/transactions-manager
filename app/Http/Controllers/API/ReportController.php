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
                WHEN transactions.due_date < NOW() AND (
                    CASE
                        WHEN is_vat_inclusive = 1 THEN transactions.amount * (1 + transactions.vat / 100)
                        ELSE transactions.amount
                    END
                ) > (
                    SELECT COALESCE(SUM(amount), 0)
                    FROM transaction_payments
                    WHERE transaction_payments.transaction_id = transactions.id
                ) THEN (
                    CASE
                        WHEN is_vat_inclusive = 1 THEN transactions.amount * (1 + transactions.vat / 100)
                        ELSE transactions.amount
                    END
                ) - (
                    SELECT COALESCE(SUM(amount), 0)
                    FROM transaction_payments
                    WHERE transaction_payments.transaction_id = transactions.id
                )
                ELSE 0
            END
        ) as overdue'),
        DB::raw('SUM(
            CASE
                WHEN transactions.due_date >= NOW() AND (
                    CASE
                        WHEN is_vat_inclusive = 1 THEN transactions.amount * (1 + transactions.vat / 100)
                        ELSE transactions.amount
                    END
                ) > (
                    SELECT COALESCE(SUM(amount), 0)
                    FROM transaction_payments
                    WHERE transaction_payments.transaction_id = transactions.id
                ) THEN (
                    CASE
                        WHEN is_vat_inclusive = 1 THEN transactions.amount * (1 + transactions.vat / 100)
                        ELSE transactions.amount
                    END
                ) - (
                    SELECT COALESCE(SUM(amount), 0)
                    FROM transaction_payments
                    WHERE transaction_payments.transaction_id = transactions.id
                )
                ELSE 0
            END
        ) as outstanding'),
        DB::raw('SUM(
            CASE
                WHEN (
                    SELECT COALESCE(SUM(amount), 0)
                    FROM transaction_payments
                    WHERE transaction_payments.transaction_id = transactions.id
                ) >= (
                    CASE
                        WHEN is_vat_inclusive = 1 THEN transactions.amount * (1 + transactions.vat / 100)
                        ELSE transactions.amount
                    END
                ) THEN (
                    CASE
                        WHEN is_vat_inclusive = 1 THEN transactions.amount * (1 + transactions.vat / 100)
                        ELSE transactions.amount
                    END
                )
                ELSE 0
            END
        ) as paid')
    )
    ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
    ->whereBetween('transactions.due_date', [$request->startDate, $request->endDate])
    ->groupBy(DB::raw('MONTH(transactions.due_date)'), DB::raw('YEAR(transactions.due_date)'))
    ->get();

        return response()->json($results);
    }
}
