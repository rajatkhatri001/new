<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactUs;
use App\Models\ContactUsPage;
use App\Models\ProudMember;
use App\Models\SocialMedia;
use App\Models\FranchiseOpportunity;

class FrontContactUsController extends Controller
{
    public function index(Request $request) {

        $banner = ContactUs::first();

        $getintuch = ContactUsPage::first();
    
        $proudmember = ProudMember::where('status', 1)->get();

        $frenchiseOpp = FranchiseOpportunity::first();

        $socialmedia = SocialMedia::where('status', 1)->get();
    
        return view('Front.contactus', compact('banner','getintuch','proudmember','socialmedia','frenchiseOpp'));
       
    } 
}
