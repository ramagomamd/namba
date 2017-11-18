<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\ZipAlbums::class,
        Commands\CrawlAlbums::class,
        Commands\CrawlSingles::class,
        Commands\ScanAlbumsCover::class,
        Commands\CrawlSites::class,
        Commands\CacheAlbums::class,
        Commands\CacheSingles::class,
        Commands\SearchableTracks::class,
        Commands\FilesUploader::class,
        Commands\RefreshCache::class,
        Commands\FixTracksTitle::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Crawl SlikourOnLife at an hour interval everyday
        $schedule->command('crawl:sites slikour singles')
                ->hourly();
        $schedule->command('crawl:sites slikour albums')
                ->hourly();

        // Crawl Songslover at 5 past each hour everyday
        $schedule->command('crawl:sites songslover singles')
                    ->hourlyAt(5);
        $schedule->command('crawl:sites songslover albums')
                    ->hourlyAt(5);

        // Crawl Fakaza at 10 past an hour interval everyday
        $schedule->command('crawl:sites fakaza')
                ->hourlyAt(10);

        // Crawl Tooxclusive at 15 past an hour interval everyday
        $schedule->command('crawl:sites tooxclusive')
                ->hourlyAt(15);

        // Crawl Albums at half an hour everyday
        $schedule->command('albums:crawl')
                ->hourlyAt(30);

        // Crawl Singles at 25 minutes to an hour everyday
        $schedule->command('singles:crawl')
                ->hourlyAt(35);

        // Scan Album Covers at 15 minutes to an hour everyday
        $schedule->command('albums:crawl')
                ->hourlyAt(45);

        $schedule->command('files:uploader datafilehost')
                ->hourlyAt(55)
                ->name('store-file-datafilehost')
                ->withoutOverlapping();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
