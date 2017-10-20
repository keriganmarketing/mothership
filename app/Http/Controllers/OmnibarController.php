<?php

namespace App\Http\Controllers;

use App\Omnibar;
use Illuminate\Http\Request;

class OmnibarController extends Controller
{
    public function create()
    {
        $omniBar = Omnibar::build();

        return response()->json($omniBar)->withHeaders([
            'Access-Control-Allow-Origin' => '*'
        ]);
    }
}
