<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Backend\Music\CrawlerRepository;
use App\Repositories\Backend\Music\AlbumRepository;

class CrawlAlbums extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'albums:crawl  {count? : Count of albums to crawl}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl All Uncrawled Albums';

    protected $crawler;

    protected $albums;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CrawlerRepository $crawler, AlbumRepository $albums)
    {
        parent::__construct();
        $this->crawler = $crawler;
        $this->albums = $albums;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $count = $this->argument('count') ?? -1;
        $albums = $this->crawler->getAlbums(['uncrawled'])->take($count)->get();

        if ($albums->isNotEmpty()) {
            $progressBar = $this->output->createProgressBar($albums->count());

            $albums->each(function($album) use ($progressBar) {
                try {
                    $this->warn("\n{$album->title} Started Crawling...");
                    $results = $this->albums->crawl((array) $album);
                } catch (Exception $e) {
                    $results = false;
                }
                $progressBar->advance();
                if ($results) {
                    $this->info("\n{$album->title} Successfully Crawled");
                } else {
                    $this->error("\n{$album->title} Could not be crawled");
                }
                
            });
        } else {
            $this->error("\nNothing to crawl here");
        }

        $this->info("\nAll done!");
    }
}
