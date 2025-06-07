<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PrivacyPolicy;
use App\Models\PrivacyPolicyPage;
use App\Models\ProudMember;

class FrontPrivacyPolicyController extends Controller
{
    public function index()
    {
       $banner= PrivacyPolicy::first();

       $content= PrivacyPolicyPage::first();

       $proudMember = ProudMember::where('status', 1)
        ->get();
        return view('Front.PrivacyPolicy', compact('banner','content','proudMember'));
       
    }
}
