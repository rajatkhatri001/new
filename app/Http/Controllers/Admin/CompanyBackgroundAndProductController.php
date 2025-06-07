<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyBackgroundProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyBackgroundAndProductController extends Controller
{
    public function edit()
    {
        $companyProfile = CompanyBackgroundProduct::firstOrNew();
        return view('Admin.CompanyBackgroundAndProducts.edit', compact('companyProfile'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'company_background_image' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'company_background_button_title' => 'required|string',
            'company_background_title' => 'required|string',
            'company_background_description' => 'required|string',
            'company_background_button_link' => 'required|string',
            'products_description' => 'required|string',
            'products_button_title' => 'required|string',
            'products_button_link' => 'required|string',
            'image_one' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_two' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_three' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_four' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'title_one' => 'required|string',
            'title_two' => 'required|string',
            'title_three' => 'required|string',
            'title_four' => 'required|string',
        ],
        [
            'company_background_image.mimes'=>'Please upload only image files of type: PNG, JPG, JPEG, WEBP',
            'company_background_image.max' => 'The file size must be less than 2 MB',
            'company_background_button_title.required' => 'The company background button title is required',
            'company_background_button_title.string' => 'The company background button title is in valid',
            'company_background_title.required' => 'The company background title is required',
            'company_background_title.string' => 'The company background title is in valid',
            'company_background_description.required' => 'The company background description is required',
            'company_background_description.string' => 'The company background description is in valid',
            'company_background_button_link.required' => 'The company background button link is required',
            'company_background_button_link.string' => 'The company background button link is in valid',
            'products_description.required' => 'The products description is required',
            'products_description.string' => 'The products description is in valid',
            'products_button_title.required' => 'The products button title is required',
            'products_button_title.string' => 'The products button title is in valid',
            'products_button_link.required' => 'The products button link is required',
            'products_button_link.string' => 'The products button link is in valid',
            'image_one.image' => 'Image One must be an image.',
        'image_one.mimes' => 'Image One must be a file of type: jpeg, png, jpg, webp.',
        'image_one.max' => 'Image One may not be greater than 2048 kilobytes.',

        'image_two.image' => 'Image Two must be an image.',
        'image_two.mimes' => 'Image Two must be a file of type: jpeg, png, jpg, webp.',
        'image_two.max' => 'Image Two may not be greater than 2048 kilobytes.',

        'image_three.image' => 'Image Three must be an image.',
        'image_three.mimes' => 'Image Three must be a file of type: jpeg, png, jpg, webp.',
        'image_three.max' => 'Image Three may not be greater than 2048 kilobytes.',

        'image_four.image' => 'Image Four must be an image.',
        'image_four.mimes' => 'Image Four must be a file of type: jpeg, png, jpg, webp.',
        'image_four.max' => 'Image Four may not be greater than 2048 kilobytes.',

        'title_one.required' => 'Title One is required.',
        'title_one.string' => 'Title One must be a string.',

        'title_two.required' => 'Title Two is required.',
        'title_two.string' => 'Title Two must be a string.',

        'title_three.required' => 'Title Three is required.',
        'title_three.string' => 'Title Three must be a string.',

        'title_four.required' => 'Title Four is required.',
        'title_four.string' => 'Title Four must be a string.',
        ]);
        $companyProfile = CompanyBackgroundProduct::firstOrNew();
        if ($request->hasFile('company_background_image')) {
            if ($companyProfile->company_background_image) {
                Storage::disk('public')->delete('uploads/companybackground/' . $companyProfile->company_background_image);
            }
            $image = $request->company_background_image;
            $imageUpload = time() . '.' . $request->company_background_image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/companybackground', $imageUpload, 'public');
            $companyProfile->company_background_image = $imageUpload;
        }

        if ($request->hasFile('image_one')) {
            if ($companyProfile->image_one) {
                Storage::disk('public')->delete('uploads/companybackground/products/image_one/' . $companyProfile->image_one);
            }
            $image = $request->image_one;
            $imageUpload = time() . '.' . $request->image_one->getClientOriginalExtension();
            $path = $image->storeAs('uploads/companybackground/products/image_one', $imageUpload, 'public');
            $companyProfile->image_one = $imageUpload;
        }

        if ($request->hasFile('image_two')) {
            if ($companyProfile->image_two) {
                Storage::disk('public')->delete('uploads/companybackground/products/image_two/' . $companyProfile->image_two);
            }
            $image = $request->image_two;
            $imageUpload = time() . '.' . $request->image_two->getClientOriginalExtension();
            $path = $image->storeAs('uploads/companybackground/products/image_two', $imageUpload, 'public');
            $companyProfile->image_two = $imageUpload;
        }

        if ($request->hasFile('image_three')) {
            if ($companyProfile->image_three) {
                Storage::disk('public')->delete('uploads/companybackground/products/image_three/' . $companyProfile->image_three);
            }
            $image = $request->image_three;
            $imageUpload = time() . '.' . $request->image_three->getClientOriginalExtension();
            $path = $image->storeAs('uploads/companybackground/products/image_three', $imageUpload, 'public');
            $companyProfile->image_three = $imageUpload;
        }

        if ($request->hasFile('image_four')) {
            if ($companyProfile->image_four) {
                Storage::disk('public')->delete('uploads/companybackground/products/image_four/' . $companyProfile->image_four);
            }
            $image = $request->image_four;
            $imageUpload = time() . '.' . $request->image_four->getClientOriginalExtension();
            $path = $image->storeAs('uploads/companybackground/products/image_four', $imageUpload, 'public');
            $companyProfile->image_four = $imageUpload;
        }

        $companyProfile->company_background_title = $request->company_background_title;
        $companyProfile->company_background_description = $request->company_background_description;
        $companyProfile->company_background_button_title = $request->company_background_button_title;
        $companyProfile->company_background_button_link = $request->company_background_button_link;
        $companyProfile->products_description = $request->products_description;
        $companyProfile->products_button_title = $request->products_button_title;
        $companyProfile->products_button_link = $request->products_button_link;
        $companyProfile->title_one = $request->title_one;
        $companyProfile->title_two = $request->title_two;
        $companyProfile->title_three = $request->title_three;
        $companyProfile->title_four = $request->title_four;
        if ($companyProfile->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect()->route('admin.company_background_prodcuts.edit');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }
}
