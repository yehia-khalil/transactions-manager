<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MonthlyReportExport;
use App\Exports\ReportExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Storage;

class GenerateReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }
    public function handle()
    {
        // Your query logic here
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
            ->whereBetween('transactions.due_date', [$this->startDate, $this->endDate])
            ->groupBy(DB::raw('MONTH(transactions.due_date)'), DB::raw('YEAR(transactions.due_date)'))
            ->get();        // Generate Excel file
        $fileName = 'MonthlyReport-' . now()->format('YmdHis') . '.xlsx';
        $path = 'reports_' . $fileName;

        Excel::store(new ReportExport($results), $path);

        // Then download the file with a valid filename
        return response()->download(storage_path("app/{$path}"), 'report_' . date('Y-m-d') . '.xlsx');

        // Code to download the file or move it to desired location
        // ...
    }
}
