<?php
namespace App\Updaters;

use App\Photo;
use App\ApiCall;
use App\Listing;
use Carbon\Carbon;
use App\Helpers\BcarOptions;
use App\Helpers\EcarOptions;
use Illuminate\Support\Facades\DB;

abstract class Updater
{
    protected $mls;
    protected $rets;
    protected $classArray;
    protected $options;

    public function __construct()
    {
        $this->mls         = new ApiCall($this->association);
        $this->rets        = $this->mls->login();
        $this->classArray  = $this->association == 'bcar' ?
            ['A', 'C', 'E', 'F', 'G', 'J'] : ['A', 'B', 'C', 'E', 'F', 'G', 'H', 'I'];
        $this->options = $this->association == 'bcar' ?
                            BcarOptions::all() : EcarOptions::all();
    }

    public function cleanEcar()
    {
        //TODO: put stuff here
    }

    protected function getLastModifiedDate($table)
    {
        $lastModified = Carbon::parse(
                DB::table($table)
                ->where('association', $this->association)
                ->pluck('date_modified')
                ->max()
            )->toAtomString();

        return $lastModified;
    }
}
