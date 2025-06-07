<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\HomeSlider;
use App\Models\Director;
use App\Models\OurDivision;
use App\Models\CompanyBackgroundProduct;
use App\Models\TrustedManufacturer;
use App\Models\ClientReview;
use App\Models\FranchiseOpportunity;
use App\Models\Blog;
use App\Models\ProudMember;
use App\Models\PcdPharmaFranchise;

class FrontHomeController extends Controller
{
    public function index() {

        $slider = HomeSlider::all();

        $directors = Director::where('status', 1)
                     ->whereNull('deleted_at')
                     ->get();
        $divisions = OurDivision::where('status', 1)
                    ->whereNull('deleted_at')
                    ->get();
        $cbop  = CompanyBackgroundProduct::first();

        $tm = TrustedManufacturer::where('status', 1)
                ->whereNull('deleted_at')
                ->get();

        $homeSlider = HomeSlider::where('status', 1)
                    ->whereNull('deleted_at')
                    ->get();

        $clientReview = ClientReview::where('status', 1)
                    ->whereNull('deleted_at')
                    ->get();

        $frenchiseOpp = FranchiseOpportunity::first();

        $blog = Blog::where('status', 1)
                ->whereNull('deleted_at')
                ->limit(6)
                ->get();

        $news = Blog::where('status', 1)
        ->where('is_this_news', 1)
        ->whereNull('deleted_at')
        ->limit(3)
        ->get();

        $proudMember = ProudMember::where('status', 1)
                    ->whereNull('deleted_at')
                    ->get();

        $pcdFrenchise = PcdPharmaFranchise::first();

        return view('Front.home', compact('slider', 'directors', 'divisions','cbop','tm','homeSlider','clientReview','frenchiseOpp','blog','proudMember','pcdFrenchise','news'));
    }
}
