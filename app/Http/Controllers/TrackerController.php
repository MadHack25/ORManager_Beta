<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\DOMcontroller as DOM;

class TrackerController extends Controller
{
    public function trackWithPrice($tracking_number,$item_state){
        return DOM::priceByTracking($tracking_number,$item_state);
    }
    public function getTrackData(){
        return DOM::trackNTakeout();
    }
}
