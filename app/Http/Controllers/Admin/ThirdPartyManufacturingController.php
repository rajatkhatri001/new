<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ThirdPartyManufacturingBanner;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Redirect;
use DataTables;

class ThirdPartyManufacturingController extends Controller
{
    public function edit()
    {
        $companyProfile = ThirdPartyManufacturingBanner::firstOrNew();
        return view('Admin.ThirdPartyManufacturing.Banner.edit', compact('companyProfile'));
    }

    public function update(Request $request)
    {

        $request->validate([
            'image' => 'required_if:old_image,==,null|image|mimes:jpeg,png,jpg,webp|max:2048',
            // 'title' => 'required|string',
            // 'description' => 'required|string',

            'manufacturing_image' => 'required_if:old_manufacturing_image,==,null|image|mimes:jpeg,png,jpg,webp|max:2048',
            'website' => 'required|string',
            'contactno' => 'required|string',
            'manufacturing_title' => 'required|string',
            'manufacturing_description' => 'required|string',
           
        ],
        [
            'image.required' => 'Please upload an image.',
            'image.required_if' => 'Please upload an image.',
            'image.max' => 'The file size must be less than 2 MB',
            // 'title.required' => 'Please enter a title.',
            // 'description.required' => 'Please enter a description.',

            'manufacturing_image.required' => 'Please upload an Manufacturing image.',
            'manufacturing_image.required_if' => 'Please upload an Manufacturing image.',
            'manufacturing_image.max' => 'The file size must be less than 2 MB',

            'website.required' => 'Please enter a Website.',
            'contactno.required' => 'Please enter a Contact No.',

            'manufacturing_title.required' => 'Please enter a Manufacturing title.',
            'manufacturing_description.required' => 'Please enter a Manufacturing description.',
        ]);

            
        $companyProfile = ThirdPartyManufacturingBanner::firstOrNew();

        if ($request->hasFile('image')) {
            if ($companyProfile->image) {
                Storage::disk('public')->delete('uploads/thirdpartymanufacturingbanner/' . $companyProfile->image);
            }
            $image = $request->image;
            $imageUpload = time() . '.' . $request->image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/thirdpartymanufacturingbanner', $imageUpload, 'public');
            $companyProfile->image = $imageUpload;
        }

        if ($request->hasFile('manufacturing_image')) {
            if ($companyProfile->manufacturing_image) {
                Storage::disk('public')->delete('uploads/manufacturing_image/' . $companyProfile->manufacturing_image);
            }
            $manufacturing_image = $request->manufacturing_image;
            $manufacturingimageUpload = time() . '.' . $request->manufacturing_image->getClientOriginalExtension();
            $path = $manufacturing_image->storeAs('uploads/manufacturing_image', $manufacturingimageUpload, 'public');
            $companyProfile->manufacturing_image = $manufacturingimageUpload;
        }


        $companyProfile->title = $request->title;
        $companyProfile->description = $request->description;

        $companyProfile->website = $request->website;
        $companyProfile->contactno = $request->contactno;
        $companyProfile->manufacturing_title = $request->manufacturing_title;
        $companyProfile->manufacturing_description = $request->manufacturing_description;

        if ($companyProfile->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect()->route('admin.third_party_manufacturing_banner.edit');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }
}
