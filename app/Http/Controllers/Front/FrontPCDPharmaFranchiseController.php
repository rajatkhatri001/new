<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\PcdPharmaFranchise;
use App\Models\PcdPharmaAdvantage;
use App\Models\QualityAssurance;
use App\Models\TrustedManufacturer;
use App\Models\OurDivision;
use App\Models\LogisticPartner;
use App\Models\ProudMember;
use App\Models\ClientReview;
use App\Models\Product;

class FrontPCDPharmaFranchiseController extends Controller
{
	public function index()
	{
		$PcdPharmaFranchiseObj = PcdPharmaFranchise::first();
		$PcdPharmaAdvantageObjs = PcdPharmaAdvantage::where('status',1)->get();
		$QualityAssuranceObjs = QualityAssurance::where('status',1)->get();
		$TrustedManufacturerObjs = TrustedManufacturer::where('status',1)->get();
		$OurDivisionObjs = OurDivision::where('status',1)->get();
		$LogisticPartnerObjs = LogisticPartner::where('status',1)->get();
		$proudMember = ProudMember::where('status', 1)
                    ->whereNull('deleted_at')
                    ->get();
		$ClientReviewObjs = ClientReview::where('status',1)->get();
		$ProductObjs = Product::where('status',1)->get();
		
		return view('Front.pcdPharmaFranchise', compact('PcdPharmaFranchiseObj','PcdPharmaAdvantageObjs','QualityAssuranceObjs','TrustedManufacturerObjs','OurDivisionObjs','LogisticPartnerObjs','proudMember','ClientReviewObjs','ProductObjs'));
	}

	public function redirectNewLaunchPage() {
		return redirect()->route('front.new-launch');
	}
}