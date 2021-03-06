<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Impression;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessListingImpression implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $listings;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($listings, $userAgent = 'na')
    {
        $this->listings = $listings;
        $this->userAgent = $userAgent;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new Impression)->logMultiple($this->listings);
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags()
    {
        return ['impression'];
    }
}
