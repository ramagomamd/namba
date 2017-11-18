<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Backend\Music\CacheRepository;

class CacheSingles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'singles:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache All Singles';

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
        $this->cache->findOrMake('singles');

        $this->info("Successfully Cached Singles");
    }
}
