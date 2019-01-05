<?php
namespace App\Cleaners;

use App\Photo;
use App\Listing;
use App\Cleaners\Cleaner;
use App\Helpers\BcarOptions;
use App\Helpers\EcarOptions;
use App\Helpers\ListingsHelper;
use Carbon\Carbon;

class EcarCleaner extends Cleaner
{
    protected $association;

    public function __construct()
    {
        $this->association = 'ecar';
        parent::__construct();
    }
}
