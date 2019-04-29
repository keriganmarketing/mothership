<?php

namespace App\Jobs;

use App\Search;
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
        $this->request = $request;
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

        foreach ($this->request as $k => $v) {
            if ($v != '') {
                $searchQuery = SearchQuery::where('search_query', $v)
                    ->where('query_type', $k)
                    ->whereDate('date', $today)->first();

                if (is_array($searchQuery) && count($searchQuery) > 0) {
                    $searchQuery->update([
                        'counter' => $searchQuery->counter + 1
                    ]);
                } else {
                    SearchQuery::create([
                        'query_type'   => $k,
                        'search_query' => $v,
                        'date'         => $today,
                        'counter'      => 1
                    ]);
                }
            }
        }
    }
}
