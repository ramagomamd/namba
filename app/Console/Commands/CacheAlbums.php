<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Backend\Music\CacheRepository;

class CacheAlbums extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'albums:cache {album?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache Albums, if id provided cache it, else cache all albums';

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
        $album = $this->argument('album');
        // dd($album);
        $this->cache->findOrMake('albums');

        $this->info("Successfully Cached Album/s");
    }
}
