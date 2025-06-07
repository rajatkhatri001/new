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
use App\Models\TermsAndCondition;
use App\Models\TermsPage;

class TermsAndConditionController extends Controller
{
        //Add or Edit Terms banner
        public function editTerms()
        {
            $terms = TermsAndCondition::firstOrNew();
            return view('Admin.Terms.edit', compact('terms'));
        }

        //Update Terms banner
        public function update(Request $request)
        {
            $request->validate([
                'banner_image' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
                // 'banner_title' => 'required|string',
                // 'banner_description' => 'required|string',
            ],
            [
                'banner_image.mimes'=>'Please upload only image files of type: PNG, JPG, JPEG, WEBP',
                'banner_image.max' => 'The file size must be less than 2 MB',
                // 'banner_title.required' => 'The banner title is required',
                // 'banner_title.string' => 'The banner title is in valid',
                // 'banner_description.required' => 'The banner description is required',
                // 'banner_description.string' => 'The banner description is in valid',
            ]);
            $careerBanner = TermsAndCondition::firstOrNew();
            if ($request->hasFile('banner_image')) {
                if ($careerBanner->banner_image) {
                    Storage::disk('public')->delete('uploads/terms/' . $careerBanner->banner_image);
                }
                $image = $request->banner_image;
                $imageUpload = uniqid() . '.' . $request->banner_image->getClientOriginalExtension();
                $path = $image->storeAs('uploads/terms', $imageUpload, 'public');
                $careerBanner->banner_image = $imageUpload;
            }

            $careerBanner->banner_title = $request->banner_title;
            $careerBanner->banner_description = $request->banner_description;
            if ($careerBanner->save()) {
                $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
                return redirect()->route('admin.terms.edit');
            } else {
                $request->session()->flash('alert-error', trans('admin_messages.something_went'));
                return redirect()->back();
            }
        }

        //Add or Edit Terms page
        public function editTermsPage()
        {
            $termsPage = TermsPage::firstOrNew();
            return view('Admin.Terms.editPage', compact('termsPage'));
        }

        //Update Terms page
        public function updateTermsPage(Request $request)
        {
            $input = $request->all();
            $request->validate([
                'title_1' => 'required|string',
                'title_2' => 'required|string',
                'title_2_description' => 'required|string',
                'terms_file_title' => 'required|string',
                // 'terms_file_name' => 'required|max:2048',
                'terms_file_name' => 'required',
            ],
            [
                'title_1.required' => 'The title is required',
                'title_2.required' => 'The title is required',
                'title_2_description.required' => 'The title is required',
                'title_2_description.string' => 'The title is in valid',
                'terms_file_title.required' => 'The title is required',
                'terms_file_name.required' => 'The file is required',
            ]);
            $careerBanner = TermsPage::firstOrNew();
            if ($request->hasFile('terms_file_name')) {
                if ($careerBanner->terms_file_name) {
                    Storage::disk('public')->delete('uploads/terms/' . $careerBanner->terms_file_name);
                }
                $image = $request->terms_file_name;
                // $imageUpload = uniqid() . '.' . $request->terms_file_name->getClientOriginalExtension();
                $path = $image->storeAs('uploads/terms', $image->getClientOriginalName(), 'public');
                $careerBanner->terms_file_name = $image->getClientOriginalName();
            }
            $careerBanner->title_1 = $request->title_1;
            $careerBanner->title_2 = $request->title_2;
            $careerBanner->title_2_description = $request->title_2_description;
            $careerBanner->terms_file_title = $request->terms_file_title;
            if ($careerBanner->save()) {
                $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
                return redirect()->route('admin.termspage.edit');
            } else {
                $request->session()->flash('alert-error', trans('admin_messages.something_went'));
                return redirect()->back();
            }
        }

}
