<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\SearchQuery;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessSearch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $request;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->request      = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $today       = Carbon::now()->toDateString();
        $searchQuery = '';

        foreach ($this->request as $r) {
            $searchQuery = SearchQuery::where('search_query', $searchQuery)
                ->whereDate('created_at', $today)->first();

            if (count($searchQuery) > 0) {
                $searchQuery->increment('counter');
            } else {
                SearchQuery::create([
                    'search_query' => $r,
                    'counter'      => 1
                ]);
            }
        }
    }
}
