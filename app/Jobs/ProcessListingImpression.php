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
        $today = Carbon::now()->toDateString();

        foreach ($this->listings as $listing) {
            $impression = Impression::where('listing_id', $listing->id)
                ->where('date', $today)->first();

            if ($impression) {
                $impression->increment('counter');
            } else {
                Impression::create([
                    'listing_id' => $listing->id,
                    'date'       => $today,
                    'counter'    => 1
                ]);
            }
        }
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags()
    {
        return ['impression', $this->userAgent];
    }
}
