<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PcdPharmaFranchise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PcdPharmaFranchiseController extends Controller
{
    //
    public function edit()
    {
        $pcdPharmaFranchise = PcdPharmaFranchise::firstOrNew();
        return view('Admin.PcdPharmaFranchise.edit', compact('pcdPharmaFranchise'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'banner_image' => 'required_if:old_banner_image,==,null|image|mimes:jpeg,png,jpg,webp|max:2048',
            // 'banner_title' => 'required|string',
            // 'banner_description' => 'required|string',

            'pharma_franchise_image' => 'required_if:old_pharma_franchise_image,==,null|image|mimes:jpeg,png,jpg,webp|max:2048',
            'pharma_franchise_title' => 'required|string',
            'pharma_franchise_button_title' => 'required|string',
            'pharma_franchise_button_url' => 'required|string',
            'pharma_franchise_description' => 'required|string',

            'pcd_image' => 'required_if:old_pcd_image,==,null|image|mimes:jpeg,png,jpg,webp|max:2048',
            'pcd_title' => 'required|string',
            // 'pcd_visit' => 'required|string',
            // 'pcd_call' => 'required|string',
            'pcd_description' => 'required|string',

            'journey_title' => 'required|string',
            'journey_customers' => 'required|string',
            'journey_manufacturing_facilities' => 'required|string',
            'journey_sku' => 'required|string',
            'journey_employees' => 'required|string',
            'journey_button_title' => 'required|string',
            'journey_button_url' => 'required|string',

            'psychocare_image' => 'required_if:old_psychocare_image,==,null|image|mimes:jpeg,png,jpg,webp|max:2048',
            'psychocare_title' => 'required|string',
            'psychocare_description' => 'required|string',
        ],
            [
                'banner_image.mimes' => 'Please upload only banner image files of type: PNG, JPG, JPEG, WEBP',
                'banner_image.image' => 'The banner image is in valid',
                'banner_image.required_if' => 'The banner image is required',
                'banner_image.max' => 'The file size must be less than 2 MB',
                // 'banner_title.required' => 'The banner title is required',
                // 'banner_title.string' => 'The banner title is in valid',
                // 'banner_description.required' => 'The banner description is required',
                // 'banner_description.string' => 'The banner description is in valid',

                'pharma_franchise_image.mimes' => 'Please upload only pharma franchise image files of type: PNG, JPG, JPEG, WEBP',
                'pharma_franchise_image.image' => 'The pharma franchise image is in valid',
                'pharma_franchise_image.required_if' => 'The pharma franchise image is required',
                'pharma_franchise_image.max' => 'The file size must be less than 2 MB',
                'pharma_franchise_title.required' => 'The pharma franchise title is required',
                'pharma_franchise_title.string' => 'The pharma franchise title is in valid',
                'pharma_franchise_description.required' => 'The pharma franchise description is required',
                'pharma_franchise_description.string' => 'The pharma franchise description is in valid',
                'pharma_franchise_button_title.required' => 'The pharma franchise button title is required',
                'pharma_franchise_button_title.string' => 'The pharma franchise button title is in valid',
                'pharma_franchise_button_url.required' => 'The pharma franchise button url is required',
                'pharma_franchise_button_url.string' => 'The pharma franchise button url is in valid',

                'pcd_image.mimes' => 'Please upload only pcd image files of type: PNG, JPG, JPEG, WEBP',
                'pcd_image.max' => 'The file size must be less than 2 MB',
                'pcd_image.image' => 'The pcd image is in valid',
                'pcd_image.required_if' => 'The pcd image is required',
                'pcd_title.required' => 'The pcd title is required',
                'pcd_title.string' => 'The pcd title is in valid',
                'pcd_description.required' => 'The pcd description is required',
                'pcd_description.string' => 'The pcd description is in valid',
                // 'pcd_call.required' => 'The pcd call is required',
                // 'pcd_call.string' => 'The pcd call is in valid',
                // 'pcd_visit.required' => 'The pcd visit link is required',
                // 'pcd_visit.string' => 'The pcd visit link is in valid',

                'pcd_journey_title.required' => 'The journey title is required',
                'pcd_journey_title.string' => 'The journey title is in valid',

                'pcd_journey_customers.required' => 'The journey customers is required',
                'pcd_journey_customers.string' => 'The journey customers is in valid',

                'pcd_journey_manufacturing_facilities.required' => 'The journey Manufacturers is required',
                'pcd_journey_manufacturing_facilities.string' => 'The journey Manufacturers is in valid',

                'pcd_journey_sku.required' => 'The journey sku is required',
                'pcd_journey_sku.string' => 'The journey sku is in valid',

                'pcd_journey_employees.required' => 'The journey employees is required',
                'pcd_journey_employees.string' => 'The journey employees is in valid',

                'pcd_journey_button_title.required' => 'The journey button title is required',
                'pcd_journey_button_title.string' => 'The journey button title is in valid',

                'pcd_journey_button_url.required' => 'The journey button url is required',
                'pcd_journey_button_url.string' => 'The journey button url is in valid',

                'psychocare_image.mimes' => 'Please upload only psychocare image files of type: PNG, JPG, JPEG, WEBP',
                'psychocare_image.image' => 'The psychocare image is in valid',
                'psychocare_image.max' => 'The file size must be less than 2 MB',
                'psychocare_image.required_if' => 'The psychocare image is required',
                'psychocare_title.required' => 'The psychocare title is required',
                'psychocare_title.string' => 'The psychocare title is in valid',
                'psychocare_description.required' => 'The psychocare description is required',
                'psychocare_description.string' => 'The psychocare description is in valid',

            ]);
        $pcdPharmaFranchise = PcdPharmaFranchise::firstOrNew();
        if ($request->hasFile('banner_image')) {
            if ($pcdPharmaFranchise->banner_image) {
                Storage::disk('public')->delete('uploads/pcdpharmafranchise/banner_image/' . $pcdPharmaFranchise->banner_image);
            }
            $image = $request->banner_image;
            $imageUpload = time() . '.' . $request->banner_image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/pcdpharmafranchise/banner_image', $imageUpload, 'public');
            $pcdPharmaFranchise->banner_image = $imageUpload;
        }
        if ($request->hasFile('pharma_franchise_image')) {
            if ($pcdPharmaFranchise->pharma_franchise_image) {
                Storage::disk('public')->delete('uploads/pcdpharmafranchise/pharma_franchise_image/' . $pcdPharmaFranchise->pharma_franchise_image);
            }
            $image = $request->pharma_franchise_image;
            $imageUpload = time() . '.' . $request->pharma_franchise_image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/pcdpharmafranchise/pharma_franchise_image', $imageUpload, 'public');
            $pcdPharmaFranchise->pharma_franchise_image = $imageUpload;
        }
        if ($request->hasFile('pcd_image')) {
            if ($pcdPharmaFranchise->pcd_image) {
                Storage::disk('public')->delete('uploads/pcdpharmafranchise/pcd_image/' . $pcdPharmaFranchise->pcd_image);
            }
            $image = $request->pcd_image;
            $imageUpload = time() . '.' . $request->pcd_image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/pcdpharmafranchise/pcd_image', $imageUpload, 'public');
            $pcdPharmaFranchise->pcd_image = $imageUpload;
        }
        if ($request->hasFile('psychocare_image')) {
            if ($pcdPharmaFranchise->psychocare_image) {
                Storage::disk('public')->delete('uploads/pcdpharmafranchise/psychocare_image/' . $pcdPharmaFranchise->psychocare_image);
            }
            $image = $request->psychocare_image;
            $imageUpload = time() . '.' . $request->psychocare_image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/pcdpharmafranchise/psychocare_image', $imageUpload, 'public');
            $pcdPharmaFranchise->psychocare_image = $imageUpload;
        }

        $pcdPharmaFranchise->banner_title = $request->banner_title;
        $pcdPharmaFranchise->banner_description = $request->banner_description;
        $pcdPharmaFranchise->banner_title = $request->banner_title;

        $pcdPharmaFranchise->pharma_franchise_title = $request->pharma_franchise_title;
        $pcdPharmaFranchise->pharma_franchise_button_title = $request->pharma_franchise_button_title;
        $pcdPharmaFranchise->pharma_franchise_button_url = $request->pharma_franchise_button_url;
        $pcdPharmaFranchise->pharma_franchise_description = $request->pharma_franchise_description;

        $pcdPharmaFranchise->pcd_title = $request->pcd_title;
        // $pcdPharmaFranchise->pcd_visit = $request->pcd_visit;
        // $pcdPharmaFranchise->pcd_call = $request->pcd_call;
        $pcdPharmaFranchise->pcd_description = $request->pcd_description;

        $pcdPharmaFranchise->journey_title = $request->journey_title;
        $pcdPharmaFranchise->journey_customers = $request->journey_customers;
        $pcdPharmaFranchise->journey_manufacturing_facilities = $request->journey_manufacturing_facilities;
        $pcdPharmaFranchise->journey_sku = $request->journey_sku;
        $pcdPharmaFranchise->journey_employees = $request->journey_employees;
        $pcdPharmaFranchise->journey_button_title = $request->journey_button_title;
        $pcdPharmaFranchise->journey_button_url = $request->journey_button_url;

        $pcdPharmaFranchise->psychocare_title = $request->psychocare_title;
        $pcdPharmaFranchise->psychocare_description = $request->psychocare_description;

        if ($pcdPharmaFranchise->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect()->route('admin.pcdpharmafranchise.edit');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }
}
