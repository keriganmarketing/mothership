<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SearchQuery extends Model
{
    protected $guarded = [];

    public function logNew($request)
    {
        $today       = Carbon::now()->toDateString();
        $searchQuery = '';

        foreach ($request as $k => $v) {            
            if ($v != '') {
                $searchQuery = SearchQuery::where('search_query', $v)
                    ->where('query_type', $k)
                    ->whereDate('date', $today)->first();

                if ($searchQuery->count() > 0) {
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
