<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CareerBanner;
use App\Models\JoinUs;
use App\Models\ProudMember;
use App\Models\CurrentOpportunites;
use App\Models\FranchiseOpportunity;


class FrontCareerController extends Controller
{
    public function index(Request $request)
    {

        $banner = CareerBanner::first();

        $joinus = joinUs::first();

        $proudmember = ProudMember::where('status', 1)->get();
        $frenchiseOpp = FranchiseOpportunity::first();

        $currentOpportunities  = CurrentOpportunites::where('status', 1)->paginate(4);
        if ($request->ajax()) {
            return view('Front.career-pagination', compact('currentOpportunities'))->render();
        }
        return view('Front.career', compact('banner', 'joinus', 'currentOpportunities', 'proudmember', 'frenchiseOpp'));
    }
}
