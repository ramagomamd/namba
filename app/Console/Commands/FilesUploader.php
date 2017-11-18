<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Music\Uploaders\Upload;

class FilesUploader extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:uploader {sitename : The website to upload to}
                            {type : The type of content to upload (zips/files)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload files to external sites';

    protected $upload;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Upload $upload)
    {
        parent::__construct();
        $this->upload = $upload;
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        $sitename = $this->argument('sitename');
        $type = $this->argument('type');
        $data = [];

        if ($sitename == 'datafilehost')  {
            $data['link'] = 'https://www.datafilehost.com/';
            $data['site'] = $sitename;
            $data['type'] = $type;

            return $this->upload->process($data);
        }

        $this->info("Successfully Finishing UploadingTracks");
    }
}
