<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ThirdPartyManufacturingBanner;
use App\Models\QualityAssurance;
use App\Models\PcdPharmaFranchise;
use App\Models\ThirdPartyManufacturingBenefits;
use App\Models\ClientReview;
use App\Models\ProductionDividedUnit;
use App\Models\DealWithRangeimage;
use App\Models\DealWithRange;
use App\Models\ManageCerificate;
use App\Models\ProudMember;

class FrontThirdPartyManufacturingController extends Controller
{
    public function store(Request $request){

        $banner = ThirdPartyManufacturingBanner::first();

        $franchise = PcdPharmaFranchise::first();

        $dealwithrange = DealWithRange::first();

        $qualityassurances = QualityAssurance::where('status',1)->get();

        $benefits = ThirdPartyManufacturingBenefits::where('status',1)->get();

        $clientReview = ClientReview::where('status', 1)->get();

        $dividedunits = ProductionDividedUnit::where('status', 1)->get();

        $dealwithrangeimages = DealWithRangeimage::where('status', 1)->get();

        $certificates = ManageCerificate::where('status', 1)->paginate(6); // Keeps the pagination
    
        if ($request->ajax() && isset($_REQUEST['certificate']) && $_REQUEST['certificate']=='certificate') {
            return view('Front.includecertificates', compact('certificates'))->render();
        }

        $proudmember = ProudMember::where('status', 1)->get();
                    

        return view('Front.thirdpartymanufacturing',compact('banner','proudmember','certificates','qualityassurances','franchise','benefits','clientReview','dividedunits','dealwithrange','dealwithrangeimages'));
    }
}
