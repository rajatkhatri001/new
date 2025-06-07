<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CorporateOfficeTour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CorporateOfficeTourController extends Controller
{
    //
    public function edit()
    {
        $corporateOfficeTour = CorporateOfficeTour::firstOrNew();
        return view('Admin.CorporateOfficeTour.edit', compact('corporateOfficeTour'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'banner_image' => 'required_if:old_banner_image,==,null|image|mimes:jpeg,png,jpg,webp|max:2048',
            // 'banner_title' => 'required|string',
            // 'banner_description' => 'required|string',

            'welcome_video_url' => 'required|string',
            'welcome_title' => 'required|string',
            'welcome_description' => 'required|string',
            'office_map_iframe' => 'required|string',
            'office_location' => 'required|string',
            'office_phone' => 'required|string',
            'office_email' => 'required|string',
            'office_website' => 'required|string',
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

                'welcome_video_url.required' => 'The welcome video url is required',
                'welcome_video_url.string' => 'The welcome video url is in valid',
                'welcome_title.required' => 'The welcome title is required',
                'welcome_title.string' => 'The welcome title is in valid',
                'welcome_description.required' => 'The welcome description is required',
                'welcome_description.string' => 'The welcome description is in valid',

                'office_map_iframe.required' => 'The office map iframe is required',
                'office_map_iframe.string' => 'The office map iframe is in valid',
                'office_location.required' => 'The office location is required',
                'office_location.string' => 'The office location is in valid',
                'office_phone.required' => 'The office phone is required',
                'office_phone.string' => 'The office phone is in valid',
                'office_email.required' => 'The office_email is required',
                'office_email.string' => 'The office_email is in valid',
                'office_website.required' => 'The office website is required',
                'office_website.string' => 'The office website is in valid',

            ]);
        $corporateOfficeTour = CorporateOfficeTour::firstOrNew();
        if ($request->hasFile('banner_image')) {
            if ($corporateOfficeTour->banner_image) {
                Storage::disk('public')->delete('uploads/corporateofficetour/banner_image/' . $corporateOfficeTour->banner_image);
            }
            $image = $request->banner_image;
            $imageUpload = time() . '.' . $request->banner_image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/corporateofficetour/banner_image', $imageUpload, 'public');
            $corporateOfficeTour->banner_image = $imageUpload;
        }
       

        $corporateOfficeTour->banner_title = $request->banner_title;
        $corporateOfficeTour->banner_description = $request->banner_description;
        $corporateOfficeTour->welcome_video_url = $request->welcome_video_url;

        $corporateOfficeTour->welcome_title = $request->welcome_title;
        $corporateOfficeTour->welcome_description = $request->welcome_description;

        $corporateOfficeTour->office_map_iframe = $request->office_map_iframe;
        $corporateOfficeTour->office_location = $request->office_location;
        $corporateOfficeTour->office_phone = $request->office_phone;
        $corporateOfficeTour->office_email = $request->office_email;
        $corporateOfficeTour->office_website = $request->office_website;

        if ($corporateOfficeTour->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect()->route('admin.corporateofficetour.edit');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }
}
