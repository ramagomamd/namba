<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Backend\Music\CrawlerRepository;
use App\Repositories\Backend\Music\SingleRepository;

class CrawlSingles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'singles:crawl  {count? : Count of albums to crawl}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl All Uncrawled Singles';

    protected $crawler;

    protected $singles;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CrawlerRepository $crawler, SingleRepository $singles)
    {
        parent::__construct();
        $this->crawler = $crawler;
        $this->singles = $singles;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $count = $this->argument('count') ?? -1;
        $singles = $this->crawler->getSingles(['uncrawled'])->take($count)->get();
        // dd($singles->count());

        if ($singles->isNotEmpty()) {
            $progressBar = $this->output->createProgressBar($singles->count());

            $singles->each(function($single) use ($progressBar) {
                try {
                    $this->warn("\n{$single->title} Started Crawling...");
                    $results = $this->singles->crawl((array) $single);
                } catch (Exception $e) {
                    $results = false;
                }
                $progressBar->advance();
                if ($results) {
                    $this->info("\n{$single->title} Successfully Crawled");
                } else {
                    $this->error("\n{$single->title} Could not be crawled");
                }
                
            });
        } else {
            $this->error("\nNothing to crawl here");
        }

        $this->info("\nAll done!");
    }
}
