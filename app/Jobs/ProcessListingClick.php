<?php

namespace App\Jobs;

use App\Click;
use App\Listing;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessListingClick implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $listing;
    protected $userAgent;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Listing $listing, $userAgent = 'na')
    {
        $this->listing = $listing;
        $this->userAgent = $userAgent;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new Click)->logNew($this->listing->id);
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags()
    {
        return ['click', $this->listing->mls_account, $this->userAgent];
    }
}
