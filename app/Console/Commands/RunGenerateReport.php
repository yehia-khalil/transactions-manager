<?php

namespace App\Console\Commands;

use App\Jobs\GenerateReport;
use Illuminate\Console\Command;

class RunGenerateReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:generate {startDate} {endDate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly financial report';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startDate = $this->argument('startDate');
        $endDate = $this->argument('endDate');
        GenerateReport::dispatch($startDate, $endDate);
    }
}
