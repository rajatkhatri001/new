<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ManageAwardsBanner;
use App\Models\ManageAwards;
use App\Models\ManageCerificate;
use App\Models\ProudMember;
use App\Models\FranchiseOpportunity;

class FrontAwardsCertificateController extends Controller
{
    public function index(Request $request) {

        $banner = ManageAwardsBanner::first();
        $proudmember = ProudMember::where('status', 1)->get();
        
        $awards = ManageAwards::where('status', 1)->paginate(6);
        $totalaward = $awards->total();

        $certificates = ManageCerificate::where('status', 1)->paginate(6); // Keeps the pagination
        $totalCertificates = $certificates->total();
        
        if ($request->ajax() && isset($_REQUEST['certificate']) && $_REQUEST['certificate']=='certificate') {
            return view('Front.includecertificates', compact('certificates'))->render();
        }

        if ($request->ajax() && isset($_REQUEST['award']) && $_REQUEST['award']=='award') {
            return view('Front.includeaward', compact('awards'))->render();
        }
    
        $frenchiseOpp = FranchiseOpportunity::first();

        return view('Front.awardcertificate', compact('totalaward','totalCertificates','banner', 'awards', 'certificates', 'proudmember','frenchiseOpp'));
       
    } 
}
