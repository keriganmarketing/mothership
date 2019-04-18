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

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Listing $listing)
    {
        $this->listing = $listing;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $today = Carbon::now()->toDateString();

        $click = Click::where('listing_id', $this->listing->id)
            ->where('date', $today)->first();

        if ($click) {
            $click->increment('counter');
        } else {
            Click::create([
                'listing_id' => $this->listing->id,
                'date'       => $today,
                'counter'    => 1
            ]);
        }
    }
}
