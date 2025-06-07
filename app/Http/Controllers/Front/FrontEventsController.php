<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EventPage;
use App\Models\Event;
use App\Models\ProudMember;
use App\Models\FranchiseOpportunity;
use App\Models\EventImage;

class FrontEventsController extends Controller
{
    public function index(Request $request) {

        $banner = EventPage::first();

         $details = Event::where('status', 1)->get();
    
        $proudmember = ProudMember::where('status', 1)->get();

        $eventimages = EventImage::where('status', 1)->get();

        $frenchiseOpp = FranchiseOpportunity::first();

    
        return view('Front.events', compact('banner','details','proudmember','frenchiseOpp','eventimages'));
    } 
}
