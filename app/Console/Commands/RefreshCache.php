<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Backend\Music\CacheRepository;

class RefreshCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refreshes the whole site cache';

    protected $cache;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CacheRepository $cache)
    {
        parent::__construct();
        $this->cache = $cache;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->cache->refresh();

        $this->info("The Cache has succesfully been refreshed");
    }
}
