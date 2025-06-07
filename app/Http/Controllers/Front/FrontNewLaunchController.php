<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\NewLaunchBanner;
use App\Models\NewLaunchSlider;
use App\Models\Product;
use App\Models\OurProductImages;
use App\Models\ProudMember;
use App\Models\FranchiseOpportunity;
use App\Models\OurProduct;

class FrontNewLaunchController extends Controller
{
    public function index(){

        $banner = NewLaunchBanner::first();

        $newProductLaunchBanner = NewLaunchSlider::where('is_banner',1)
                                ->where('status', 1)
                                ->whereNull('deleted_at')
                                ->get();

        $newProduct = Product::where('is_new_product',1)
                                ->where('status', 1)
                                ->whereNull('deleted_at')
                                ->get();

        $newProductLaunch = NewLaunchSlider::
                            where('status', 1)
                            ->whereNull('deleted_at')
                            ->get();

        $ourProduct = OurProductImages::where('is_new_product',1)
                    ->where('status', 1)
                    ->whereNull('deleted_at')
                    ->get();

        $proudMember = ProudMember::where('status', 1)
                    ->whereNull('deleted_at')
                    ->get();

        $frenchiseOpp = FranchiseOpportunity::first();


        return view('Front.newLaunch', compact('banner','newProductLaunchBanner','newProduct','newProductLaunch','ourProduct','proudMember','frenchiseOpp'));
    }

    public function newLaunchDetails($id = null)
    {
        if ($id) {
            $ourProduct = OurProduct::first();
            $proudMember = ProudMember::where('status', 1)->get();
            $product = OurProductImages::where('id', $id)->first();
            // print_r($product); exit;
            // $productCategory = $product->productCategory;
            // $division = $productCategory->divisions;
            // $similerProducts = OurProductImages::whereHas('productCategory', function ($query) use ($division) {
            //     $query->where('division', $division->id);
            // })->where('id', '!=', $product->id)->where('status', '1')->latest()->take(3)->get();

            return view('Front.product-details', compact('product', 'proudMember', 'ourProduct'));
        } else {
            return redirect()->back();
        }
    }
}
