<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Music\Crawlers\Crawler;

class CrawlSites extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl:sites  
                            {sitename : The website to crawl}
                            {type? : The type of content to crawl (albums/singles). If not selected, crawl all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl A Specified Website';

    protected $crawler;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Crawler $crawler)
    {
        parent::__construct();
        $this->crawler = $crawler;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $sitename = $this->argument('sitename');
        $data = [];

        if ($sitename == 'fakaza')  {
            $data['url'] = 'http://fakaza.com/download-mp3/';
            $data['site'] = $sitename;
            $data['category'] = 'south african';
            $data['genre'] = 'hip hop';
            $data['pages'] = 10;
        } elseif ($sitename == 'tooxclusive')  {
            $data['url'] = 'http://tooxclusive.com/main/download-mp3/';
            $data['site'] = $sitename;
            $data['category'] = 'nigerian';
            $data['genre'] = 'hip hop';
            $data['pages'] = 1;
        } elseif ($sitename == 'slikour')  {
            if ($this->hasArgument('type')) {
                $type = $this->argument('type');
                switch ($type) {
                    case 'albums':
                        $data['url'] = 'http://slikouronlife.co.za/downloads';
                        $data['category'] = 'south african';
                        $data['genre'] = 'hip hop';
                        $data['site'] = $sitename;
                        $data['type'] = 'albums';
                        break;
                    
                    case 'singles':
                        $data['url'] = 'http://slikouronlife.co.za/downloads';
                        $data['category'] = 'south african';
                        $data['genre'] = 'hip hop';
                        $data['site'] = $sitename;
                        $data['type'] = 'singles';
                        break;

                    default:
                        $data = null;
                        break;
                }
            } else {
                $data = null;
            }
        } elseif ($sitename == 'songslover')  {
            if ($this->hasArgument('type')) {
                $type = $this->argument('type');
                switch ($type) {
                    case 'albums':
                        $data['url'] = 'https://m.songslover.club/category/albums';
                        $data['category'] = 'american';
                        $data['genre'] = 'hip hop';
                        $data['site'] = $sitename;
                        $data['pages'] = 1;
                        $data['type'] = 'albums';
                        break;
                    
                    case 'singles':
                        $data['url'] = 'https://m.songslover.club/category/tracks';
                        $data['category'] = 'american';
                        $data['genre'] = 'hip hop';
                        $data['site'] = $sitename;
                        $data['pages'] = 1;
                        $data['type'] = 'singles';
                        break;

                    default:
                        $data = null;
                        break;
                }
            } else {
                $data = null;
            }
        } elseif ($sitename == 'lulamusic')  {
            if ($this->hasArgument('type')) {
                $type = $this->argument('type');
                switch ($type) {
                    case 'albums':
                        $data['url'] = 'https://songslover.club/albums/';
                        $data['category'] = 'american';
                        $data['genre'] = 'hip hop';
                        $data['site'] = $sitename;
                        $data['pages'] = 10;
                        break;
                    
                    case 'singles':
                        $data['url'] = 'http://lulamusic.dev/south-african/hip-hop/singles';
                        $data['category'] = 'south african';
                        $data['genre'] = 'hip hop';
                        $data['site'] = $sitename;
                        $data['pages'] = 10;
                        break;

                    default:
                        $data = null;
                        break;
                }
            } else {
                $data = null;
            }
        }

        if (!is_null($data)) {
            $this->warn("Started scanning {$data['site']} for crawling...");
            $results = $this->crawler->process($data); 
            $this->info("Finished crawling {$data['site']} with {$results} scanned links...");
        } else {
            $this->error("\nNothing to crawl here");
        }
        
        $this->info("\nAll done!");
    }
}
