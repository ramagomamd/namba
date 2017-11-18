<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Music\Track\Track;

class SearchableTracks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tracks:search';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syn Tracks To Algolia Search';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tracks = Track::whereNotNull('trackable_id')
                        ->whereNotNull('slug')
                        ->orderBy('created_at', 'desc')
                        ->searchable();


        $this->info("Successfully Finishing Syncing Algolia");
    }
}
