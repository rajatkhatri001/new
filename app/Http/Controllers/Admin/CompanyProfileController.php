<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\ManageCerificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyProfileController extends Controller
{
    //
    public function edit()
    {
        $companyProfile = CompanyProfile::firstOrNew();
        return view('Admin.CompanyProfile.edit', compact('companyProfile'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'banner_image' => 'required_if:old_banner_image,==,null|image|mimes:jpeg,png,jpg,webp|max:2048',
            // 'banner_title' => 'required|string',
            // 'banner_description' => 'required|string',

            'about_pchpl_title' => 'required|string',
            'about_pchpl_description' => 'required|string',
            'video_url' => 'required|string',

            'mission_image' => 'required_if:old_mission_image,==,null|image|mimes:jpeg,png,jpg,webp|max:2048',
            'mission_title' => 'required|string',
            // 'mission_button_title' => 'required|string',
            // 'mission_button_url' => 'required|string',
            'mission_description' => 'required|string',

            'vision_image' => 'required_if:old_vision_image,==,null|image|mimes:jpeg,png,jpg,webp|max:2048',
            'vision_title' => 'required|string',
            // 'vision_button_title' => 'required|string',
            // 'vision_button_url' => 'required|string',
            'vision_description' => 'required|string',

            'we_believe_image' => 'required_if:old_we_believe_image,==,null|image|mimes:jpeg,png,jpg,webp|max:2048',
            'we_believe_title' => 'required|string',

            'achievements_title' => 'required|string',
            'achievements_button_title' => 'required|string',
            'achievements_button_url' => 'required|string',

            'directors_title' => 'required|string',
            'trusted_partners_title' => 'required|string',
            'trusted_partners_description' => 'required|string',

            'division_sister_concerns_title' => 'required|string',
            'products_title' => 'required|string',
            'pchpl_team_title' => 'required|string',
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

                'about_pchpl_title.required' => 'The about pchpl title is required',
                'about_pchpl_title.string' => 'The about pchpl title is in valid',
                'about_pchpl_description.required' => 'The about pchpl description is required',
                'about_pchpl_description.string' => 'The about pchpl description is in valid',
                'video_url.required' => 'The video url is required',
                'video_url.string' => 'The video url is in valid',

                'mission_image.mimes' => 'Please upload only mission image files of type: PNG, JPG, JPEG, WEBP',
                'mission_image.image' => 'The mission image is in valid',
                'mission_image.required_if' => 'The mission image is required',
                'mission_image.max' => 'The file size must be less than 2 MB',

                'mission_title.required' => 'The mission title is required',
                'mission_title.string' => 'The mission title is in valid',
                'mission_description.required' => 'The mission description is required',
                'mission_description.string' => 'The mission description is in valid',
                // 'mission_button_title.required' => 'The mission button title is required',
                // 'mission_button_title.string' => 'The mission button title is in valid',
                // 'mission_button_url.required' => 'The mission button url is required',
                // 'mission_button_url.string' => 'The mission button url is in valid',

                'vision_image.mimes' => 'Please upload only vision image files of type: PNG, JPG, JPEG, WEBP',
                'vision_image.image' => 'The vision image is in valid',
                'vision_image.required_if' => 'The vision image is required',
                'vision_image.max' => 'The file size must be less than 2 MB',

                'vision_title.required' => 'The vision title is required',
                'vision_title.string' => 'The vision title is in valid',
                'vision_description.required' => 'The vision description is required',
                'vision_description.string' => 'The vision description is in valid',
                // 'vision_button_title.required' => 'The vision button title is required',
                // 'vision_button_title.string' => 'The vision button title is in valid',
                // 'vision_button_url.required' => 'The vision button url is required',
                // 'vision_button_url.string' => 'The vision button url is in valid',

                'we_believe_image.mimes' => 'Please upload only we believe image files of type: PNG, JPG, JPEG, WEBP',
                'we_believe_image.image' => 'The we believe image is in valid',
                'we_believe_image.required_if' => 'The we believe image is required',
                'we_believe_image.max' => 'The file size must be less than 2 MB',

                'we_believe_title.required' => 'The we believe title is required',
                'we_believe_title.string' => 'The we believe title is in valid',
                

                'achievements_title.required' => 'The achievements title is required',
                'achievements_title.string' => 'The achievements title is in valid',
                'achievements_button_title.required' => 'The achievements button title is required',
                'achievements_button_title.string' => 'The achievements button title is in valid',
                'achievements_button_url.required' => 'The achievements button url is required',
                'achievements_button_url.string' => 'The achievements button url is in valid',

                'directors_title.required' => 'The directors title is required',
                'directors_title.string' => 'The directors title is in valid',

                'trusted_partners_title.required' => 'The trusted partners title is required',
                'trusted_partners_title.string' => 'The trusted partners title is in valid',
                'trusted_partners_description.required' => 'The trusted partners description is required',
                'trusted_partners_description.string' => 'The trusted partners description is in valid',

                'division_sister_concerns_title.required' => 'The division & sister concerns title is required',
                'division_sister_concerns_title.string' => 'The division & sister concerns title is in valid',

                'products_title.required' => 'The products title is required',
                'products_title.string' => 'The products title is in valid',

                'pchpl_team_title.required' => 'The pchpl team title is required',
                'pchpl_team_title.string' => 'The pchpl team title is in valid',
            ]);
        $companyProfile = CompanyProfile::firstOrNew();
        if ($request->hasFile('banner_image')) {
            if ($companyProfile->banner_image) {
                Storage::disk('public')->delete('uploads/companyprofile/banner_image/' . $companyProfile->banner_image);
            }
            $image = $request->banner_image;
            $imageUpload = time() . '.' . $request->banner_image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/companyprofile/banner_image', $imageUpload, 'public');
            $companyProfile->banner_image = $imageUpload;
        }
        if ($request->hasFile('mission_image')) {
            if ($companyProfile->mission_image) {
                Storage::disk('public')->delete('uploads/companyprofile/mission_image/' . $companyProfile->mission_image);
            }
            $image = $request->mission_image;
            $imageUpload = time() . '.' . $request->mission_image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/companyprofile/mission_image', $imageUpload, 'public');
            $companyProfile->mission_image = $imageUpload;
        }
        if ($request->hasFile('vision_image')) {
            if ($companyProfile->vision_image) {
                Storage::disk('public')->delete('uploads/companyprofile/vision_image/' . $companyProfile->vision_image);
            }
            $image = $request->vision_image;
            $imageUpload = time() . '.' . $request->vision_image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/companyprofile/vision_image', $imageUpload, 'public');
            $companyProfile->vision_image = $imageUpload;
        }
        if ($request->hasFile('we_believe_image')) {
            if ($companyProfile->we_believe_image) {
                Storage::disk('public')->delete('uploads/companyprofile/we_believe_image/' . $companyProfile->we_believe_image);
            }
            $image = $request->we_believe_image;
            $imageUpload = time() . '.' . $request->we_believe_image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/companyprofile/we_believe_image', $imageUpload, 'public');
            $companyProfile->we_believe_image = $imageUpload;
        }

        $companyProfile->banner_title = $request->banner_title;
        $companyProfile->banner_description = $request->banner_description;
        $companyProfile->banner_title = $request->banner_title;
        $companyProfile->about_pchpl_title = $request->about_pchpl_title;
        $companyProfile->about_pchpl_description = $request->about_pchpl_description;
        $companyProfile->video_url = $request->video_url;
        $companyProfile->mission_title = $request->mission_title;
        $companyProfile->mission_button_title = $request->mission_button_title;
        $companyProfile->mission_button_url = $request->mission_button_url;
        $companyProfile->mission_description = $request->mission_description;
        $companyProfile->vision_title = $request->vision_title;
        $companyProfile->vision_button_title = $request->vision_button_title;
        $companyProfile->vision_button_url = $request->vision_button_url;
        $companyProfile->vision_description = $request->vision_description;
        $companyProfile->we_believe_title = $request->we_believe_title;
        $companyProfile->achievements_title = $request->achievements_title;
        $companyProfile->achievements_button_title = $request->achievements_button_title;
        $companyProfile->achievements_button_url = $request->achievements_button_url;
        $companyProfile->directors_title = $request->directors_title;
        $companyProfile->trusted_partners_title = $request->trusted_partners_title;
        $companyProfile->trusted_partners_description = $request->trusted_partners_description;
        $companyProfile->division_sister_concerns_title = $request->division_sister_concerns_title;
        $companyProfile->products_title = $request->products_title;
        $companyProfile->pchpl_team_title = $request->pchpl_team_title;

        
        if ($companyProfile->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect()->route('admin.companyprofile.edit');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }
}
