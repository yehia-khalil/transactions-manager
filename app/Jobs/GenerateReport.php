<?php

namespace App\Jobs;

use App\Exports\ReportExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

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
            ->get();
        // Generate Excel file
        $fileName = 'MonthlyReport-'.now()->format('YmdHis').'.xlsx';
        $path = 'reports_'.$fileName;

        Excel::store(new ReportExport($results), $path);

        // Then download the file with a valid filename
        return response()->download(storage_path("app/{$path}"), 'report_'.date('Y-m-d').'.xlsx');

        // Code to download the file or move it to desired location
        // ...
    }
}
