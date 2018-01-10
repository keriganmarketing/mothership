<?php
namespace App\Cleaners;

use App\ApiCall;
use App\Helpers\BcarOptions;
use App\Helpers\EcarOptions;

abstract class Cleaner
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

}