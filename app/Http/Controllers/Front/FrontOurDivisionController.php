<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\OurDivisionBanner;
use App\Models\ProudMember;
use App\Models\FranchiseOpportunity;
use App\Models\OurDivision;
use App\Models\OurDivisionProduct;
use App\Models\DownloadPDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FrontOurDivisionController extends Controller
{
    public function index(){
        $banner = OurDivisionBanner::first();

        $proudMember = ProudMember::where('status', 1)
                    ->whereNull('deleted_at')
                    ->get();

        $frenchiseOpp = FranchiseOpportunity::first();

        $ourDivisions = OurDivision::where('status',1)
                    ->whereNull('deleted_at')
                    ->get();

        $products = OurDivisionProduct::where('status', 1)->whereNull('deleted_at')->get();

        return view('Front.ourDivision', compact('banner','proudMember','frenchiseOpp','ourDivisions','products'));

    }

    public function showDivision(Request $request, $divisionID = null, $categoryId = null)
    {
        $banner = OurDivisionBanner::first();
        $proudMember = ProudMember::where('status', 1)
                    ->whereNull('deleted_at')
                    ->get();
        $ourDivisions = OurDivision::where('status', 1)->get();
        $firstOurDivision = null;
        $firstOurDivisionList = null;
        if (isset($ourDivisions) && count($ourDivisions)) {
            $firstOurDivision = $ourDivisions[0];
            $firstOurDivisionList = $ourDivisions[0];
        }
        if ($divisionID) {
            $products = OurDivisionProduct::query()
                ->where('division_id', $divisionID);
            if ($categoryId !== null) {
                $products->where('category_id', $categoryId);
            } else {
                $products->where('category_id', 1);
            }
        } else {
            $divisionID = $firstOurDivision->id;
            $products = OurDivisionProduct::query()
                ->where('division_id', $firstOurDivision->id);
            if ($categoryId !== null) {
                $products->where('category_id', $categoryId);
            } else {
                $products->where('category_id', 1);
            }
        }
        $products = $products->paginate(6);
        return view('Front.ourDivision', compact('banner','ourDivisions','firstOurDivision','firstOurDivisionList','proudMember','products','divisionID','categoryId'));
    }

    public function downloadPdf(Request $request)
    {
        $downloadPdf = new DownloadPDF;
        $downloadPdf->name = $request->name;
        $downloadPdf->email = $request->email;
        $downloadPdf->save();
        $downloadPdfData = OurDivision::find($request->division_id);
        if (!$downloadPdfData || !$downloadPdfData->download_pdf) {
            return redirect()->back()->with('error', 'PDF not found.');
        }
        $pdfFileName = $downloadPdfData->download_pdf;
        $filePath = 'uploads/Ourdivision/DownloadPdf/' . $pdfFileName; 
        if (!Storage::disk('public')->exists($filePath)) {
            return redirect()->back()->with('error', 'PDF not found.');
        }
        return Storage::disk('public')->download($filePath);
    }
}