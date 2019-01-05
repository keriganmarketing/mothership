<?php
namespace App\Cleaners;

use App\Photo;
use App\Listing;
use Carbon\Carbon;
use App\Cleaners\Cleaner;
use App\Helpers\BcarOptions;
use App\Helpers\ListingsHelper;

class BcarCleaner extends Cleaner
{
    protected $association;

    public function __construct()
    {
        $this->association = 'bcar';
        parent::__construct();
    }
}