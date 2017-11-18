<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Backend\Music\AlbumRepository;

class ScanAlbumsCover extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'albums:scan-covers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan Albums Tracks for covers if album has no cover';

    protected $albums;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(AlbumRepository $albums)
    {
        parent::__construct();
        $this->albums = $albums;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $albums = $this->albums->getAll();

        if ($albums->isNotEmpty()) {
            $progressBar = $this->output->createProgressBar($albums->count());

            $albums->each(function($album) use ($progressBar) {
                try {
                    $this->warn("\n{$album->title} Started Scanning...");
                    $results = $this->albums->scanTracksForCover($album);
                } catch (Exception $e) {
                    $results = false;
                }
                $progressBar->advance();
                if ($results) {
                    $this->info("\n{$album->title} Successfully Scanned");
                } else {
                    $this->error("\n{$album->title} Could not be Scanned");
                }
                
            });
        } else {
            $this->error("\nNothing to scan here");
        }

        $this->info("\nAll done!");
    }
}
