<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Models\JoinUs;
use App\Models\CurrentOpportunites;
use Redirect;
use DataTables;
use App\Models\PrivacyPolicy;
use App\Models\PrivacyPolicyPage;

class PrivacyPolicyController extends Controller
{
    // PrivacyPolicy Banner edit
    public function editPolicy()
    {
        $privacyPolicy = PrivacyPolicy::firstOrNew();
        return view('Admin.PrivacyPolicy.edit', compact('privacyPolicy'));
    }

    //Update PrivacyPolicy banner
    public function update(Request $request)
    {
        $request->validate([
            'banner_image' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            // 'banner_title' => 'required|string',
            // 'banner_description' => 'required|string',
        ],
        [
            'banner_image.required'=>'The Image is required',
            'banner_image.mimes'=>'Please upload only image files of type: PNG, JPG, JPEG, WEBP',
            'banner_image.max' => 'The file size must be less than 2 MB',
            // 'banner_title.required' => 'The banner title is required',
            // 'banner_title.string' => 'The banner title is in valid',
            // 'banner_description.required' => 'The banner description is required',
            // 'banner_description.string' => 'The banner description is in valid',
        ]);
        $privacypolicyBanner = PrivacyPolicy::firstOrNew();
        if ($request->hasFile('banner_image')) {
            if ($privacypolicyBanner->banner_image) {
                Storage::disk('public')->delete('uploads/privacypolicy/' . $privacypolicyBanner->banner_image);
            }
            $image = $request->banner_image;
            $imageUpload = uniqid() . '.' . $request->banner_image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/privacypolicy', $imageUpload, 'public');
            $privacypolicyBanner->banner_image = $imageUpload;
        }

        $privacypolicyBanner->banner_title = $request->banner_title;
        $privacypolicyBanner->banner_description = $request->banner_description;
        if ($privacypolicyBanner->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect()->route('admin.privacyPolicy.edit');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    // PrivacyPolicy Page edit
    public function editPrivacyPolicyPage()
    {
        $privacyPolicy = PrivacyPolicyPage::firstOrNew();
        return view('Admin.PrivacyPolicy.editprivacypolicyPage', compact('privacyPolicy'));
    }

    //Update PrivacyPolicy Page
    public function updatePrivacyPolicyPage(Request $request)
    {
        $request->validate([
            'privacyPolicy_page_content' => 'required|string',
        ],
        [
            'privacyPolicy_page_content.required' => 'The banner description is required',
            'privacyPolicy_page_content.string' => 'The banner description is in valid',
        ]);
        $privacypolicyBanner = PrivacyPolicyPage::firstOrNew();

        $privacypolicyBanner->privacyPolicy_page_content = $request->privacyPolicy_page_content;
        if ($privacypolicyBanner->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect()->route('admin.privacyPolicy.editPage');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

}
