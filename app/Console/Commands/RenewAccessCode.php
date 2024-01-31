<?php

namespace App\Console\Commands;

use App\Actions\GenerateNewAccessCode;
use App\Services\Exivo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class RenewAccessCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:renew-access-code';

    public $exivo;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Renew access code';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Exivo $exivo)
    {
        parent::__construct();

        $this->exivo = $exivo;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        (new GenerateNewAccessCode)->run();

        return 0;
    }
}
