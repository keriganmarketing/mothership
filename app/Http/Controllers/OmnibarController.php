<?php
namespace App\Http\Controllers;

use App\Omnibar;
use Illuminate\Http\Request;

class OmnibarController extends Controller
{
    /**
     * Gather the data for the Omnibar used on the search page and send to the
     * requestor
     *
     * @return Http\Illuminate\Response
     */
    public function create()
    {
        $omniBar = Omnibar::build();

        return response()->json($omniBar)->withHeaders(
            [
            'Access-Control-Allow-Origin' => '*'
            ]
        );
    }
}
