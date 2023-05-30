<?php

namespace NadzorServera\Skijasi\Commands;

use Illuminate\Console\Command;
use NadzorServera\Skijasi\Helpers\Firebase\FirebasePublishFile;

class SkijasiFirebaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'skijasi:firebase-sw';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make file worker js firebase notification';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        FirebasePublishFile::publishNow();
        $this->info('firebase-messaging-sw.json created');
    }
}
