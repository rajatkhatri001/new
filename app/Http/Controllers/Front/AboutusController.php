<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\CompanyProfile;
use App\Models\ContractManufacturer;
use App\Models\CorporateOfficeTour;
use App\Models\CorporateOfficeTourImage;
use App\Models\Director;
use App\Models\DivisionAndSisterConcern;
use App\Models\FranchiseOpportunity;
use App\Models\ManageAwards;
use App\Models\OurDivision;
use App\Models\OurProduct;
use App\Models\OurProductImages;
use App\Models\OurProductsCategory;
use App\Models\PCHPLTeam;
use App\Models\ProudMember;
use App\Models\AchievementTitleDescription;
use App\Models\TrustedPartner;
use App\Models\WeBelievePoint;
use Illuminate\Http\Request;

class AboutusController extends Controller
{
    //

    public function companyProfile()
    {
        $companyProfile = CompanyProfile::first();
        $weBelievePoints = WeBelievePoint::where('status', 1)->get();
        $achievements = Achievement::where('status', 1)->get();
        $directors = Director::where('status', 1)->get();
        $trustedPartners = TrustedPartner::where('status', 1)->get();
        $divisionAndSisterConcerns = DivisionAndSisterConcern::where('status', 1)->get();
        $contractManufacturers = ContractManufacturer::where('status', 1)->get();
        $PCHPLTeams = PCHPLTeam::where('status', 1)->get();
        $proudMember = ProudMember::where('status', 1)->get();
        $frenchiseOpp = FranchiseOpportunity::first();
        $ourProductImages = OurProductImages::where('status', 1)->latest()->take(6)->get();
        return view('Front.Aboutus.company-profile', compact('companyProfile', 'weBelievePoints', 'achievements', 'directors', 'trustedPartners', 'divisionAndSisterConcerns', 'contractManufacturers', 'PCHPLTeams', 'proudMember', 'frenchiseOpp', 'ourProductImages'));
    }

    public function directorDesk()
    {
        $companyProfile = CompanyProfile::first();
        $achievements = Achievement::where('status', 1)->latest()->take(4)->get();
        $directors = Director::where('status', 1)->get();
        $proudMember = ProudMember::where('status', 1)->get();
        $frenchiseOpp = FranchiseOpportunity::first();
        $titleDes = AchievementTitleDescription::first();
        $manageAwards = ManageAwards::where('status', 1)->latest()->take(6)->get();
        return view('Front.Aboutus.director-desk', compact('companyProfile', 'achievements', 'directors', 'proudMember', 'frenchiseOpp', 'manageAwards','titleDes'));
    }

    public function corporateOfficeTour()
    {
        $corporateOfficeTour = CorporateOfficeTour::first();
        $proudMember = ProudMember::where('status', 1)->get();
        $frenchiseOpp = FranchiseOpportunity::first();
        $PCHPLTeams = PCHPLTeam::where('status', 1)->get();

        $allCorporateOfficeTourImages = CorporateOfficeTourImage::where('status', 1)->get();
        $officeCorporateOfficeTourImages = CorporateOfficeTourImage::where('status', 1)->where('category', 'office')->get();
        $catalogueCorporateOfficeTourImages = CorporateOfficeTourImage::where('status', 1)->where('category', 'catalogue')->get();
        $certificateCorporateOfficeTourImages = CorporateOfficeTourImage::where('status', 1)->where('category', 'certificate')->get();
        $gamesCorporateOfficeTourImages = CorporateOfficeTourImage::where('status', 1)->where('category', 'games')->get();
        $prizeCorporateOfficeTourImages = CorporateOfficeTourImage::where('status', 1)->where('category', 'prize')->get();

        return view('Front.Aboutus.corporate-office-tour', compact('corporateOfficeTour', 'proudMember', 'frenchiseOpp', 'PCHPLTeams', 'allCorporateOfficeTourImages', 'officeCorporateOfficeTourImages', 'catalogueCorporateOfficeTourImages', 'certificateCorporateOfficeTourImages', 'gamesCorporateOfficeTourImages', 'prizeCorporateOfficeTourImages'));
    }

    public function ourProducts(Request $request, $divisionID = null, $categoryId = null)
    {
        $ourProduct = OurProduct::first();
        $proudMember = ProudMember::where('status', 1)->get();
        $frenchiseOpp = FranchiseOpportunity::first();
        $ourDivisions = OurDivision::where('status', 1)->get();

        $firstOurDivision = null;
        $firstOurDivisionList = null;
        if (isset($ourDivisions) && count($ourDivisions)) {
            $firstOurDivision = $ourDivisions[0];
            $firstOurDivisionList = $ourDivisions[0];
        }
        $searchQuery = $request->query('search');
        if (isset($searchQuery) && $searchQuery != null) {
            $products = OurProductImages::where('product_label', 'LIKE', '%' . $searchQuery . '%')->orWhere('product_title', 'LIKE', '%' . $searchQuery . '%')->orWhere('packing_type', 'LIKE', '%' . $searchQuery . '%')->where('status', 1)->paginate(6);
            $firstOurDivisionList = null;
            $divisionID = null;
            $categoryId = null;
            return view('Front.our-products', compact('ourProduct', 'proudMember', 'frenchiseOpp', 'ourDivisions', 'firstOurDivision', 'products', 'divisionID', 'categoryId', 'firstOurDivisionList', 'searchQuery'));
        }

        if ($divisionID) {
            $productsQuery = OurProductImages::whereHas('productCategory', function ($query) use ($divisionID, $categoryId) {
                $query->where('division', $divisionID);
                if ($categoryId !== null) {
                    $query->where('id', $categoryId);
                }
            });
            if ($categoryId === null) {
                $products = $productsQuery->paginate(6);
            } else {
                $products = $productsQuery->paginate(6);
            }
        } else {
            $productsQuery = OurProductImages::whereHas('productCategory', function ($query) use ($firstOurDivision, $categoryId) {
                $query->where('division', $firstOurDivision->id);
                if ($categoryId !== null) {
                    $query->where('id', $categoryId);
                }
            });
            if ($categoryId === null) {
                $products = $productsQuery->paginate(6);
            } else {
                $products = $productsQuery->paginate(6);
            }
        }

        return view('Front.our-products', compact('ourProduct', 'proudMember', 'frenchiseOpp', 'ourDivisions', 'firstOurDivision', 'products', 'divisionID', 'categoryId', 'firstOurDivisionList', 'searchQuery'));
    }

    public function fetchCategories(Request $request)
    {
        $divisionId = $request->input('division_id');
        $perPage = $request->input('per_page', 10); // Default per page to 10 if not provided
        $saerch = $request->input('saerch');
        $categories = OurProductsCategory::where('status', 1)->where('division', $divisionId);
        if ($saerch) {
            $categories = $categories->where('category_name', 'LIKE', '%' . $saerch . '%');
        }
        $categories = $categories->paginate($perPage);
        return response()->json($categories);
    }

    public function productsDetails($id = null)
    {
        if ($id) {
            $ourProduct = OurProduct::first();
            $proudMember = ProudMember::where('status', 1)->get();
            $product = OurProductImages::where('id', $id)->first();
            $productCategory = $product->productCategory;
            $division = $productCategory->divisions;
            $similerProducts = OurProductImages::whereHas('productCategory', function ($query) use ($division) {
                $query->where('division', $division->id);
            })->where('id', '!=', $product->id)->where('status', '1')->latest()->take(3)->get();

            return view('Front.product-details', compact('product', 'proudMember', 'similerProducts', 'ourProduct'));
        } else {
            return redirect()->back();
        }
    }

    public function fetchNewProducts(Request $request)
    {
        $perPage = $request->input('per_page', 3);
        $OurProductImages = OurProductImages::where('is_new_product',1)
        ->where('status', 1)
        ->whereNull('deleted_at')
        ->paginate($perPage);
        return response()->json($OurProductImages);
    }

}
