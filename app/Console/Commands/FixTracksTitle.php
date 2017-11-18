<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Backend\Music\TrackRepository;
use Exception;

class FixTracksTitle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tracks:fix-titles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix all tracks titles';

    protected $tracks;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TrackRepository $tracks)
    {
        parent::__construct();
        $this->tracks = $tracks;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tracks = $this->tracks->query()
                ->whereNotNull('trackable_id')
                ->get()
                ->reject(function($track) {
                    return is_null($track->trackable);
                });

        if ($tracks->isNotEmpty()) {
            $progressBar = $this->output->createProgressBar($tracks->count());

            $tracks->each(function($track) use ($progressBar) {
                try {
                    $this->warn("\nScanning {$track->full_title} ...");
                    $results = $this->tracks->updateTitle($track, $track->full_title);
                } catch (Exception $e) {
                    $results = false;
                }
                $progressBar->advance();
                if ($results) {
                    $this->info("\n{$track->full_title} Successfully Fixed");
                } else {
                    $this->error("\n{$track->full_title} Could not be scanned");
                }
                
            });
        } else {
            $this->error("\nNothing to scan here");
        }

        $this->info("\nAll done!");
    }
}
