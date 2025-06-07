<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TermsAndCondition;
use App\Models\TermsPage;
use App\Models\ProudMember;
use App\Models\FranchiseOpportunity;
use Illuminate\Support\Facades\Storage;

class FrontTermsAndConditions extends Controller
{
    public function index(){

        $banner = TermsAndCondition::first();
        $frenchiseOpp = FranchiseOpportunity::first();


        $details = TermsPage::first();
     
        $proudMember = ProudMember::where('status', 1)
        ->get();
        return view('Front.TermsAndConditions',compact('banner','details','proudMember','frenchiseOpp'));

    }

    public function downloadUserPDF($filename)
    {
        $filePath = 'uploads/terms/' . $filename;  // Adjust path according to the root of the 'public' disk

    if (!Storage::disk('public')->exists($filePath)) {
        Log::error("File not found at: $filePath");  // Ensure logging to see if this triggers
        abort(404);
    }

    return Storage::disk('public')->download($filePath);
    
    }
}
