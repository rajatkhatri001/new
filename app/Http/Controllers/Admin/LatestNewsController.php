<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LatestNews;
use Illuminate\Support\Facades\Storage;

class LatestNewsController extends Controller
{
    public function edit()
    {
        $blogPage = LatestNews::firstOrNew();
        return view('Admin.LatestNewsPage.edit', compact('blogPage', ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'banner_image' => 'required_if:old_banner_image,==,null|image|mimes:jpeg,png,jpg,webp|max:2048',

        ],
            [
                'banner_image.mimes' => 'Please upload only banner image files of type: PNG, JPG, JPEG, WEBP',
                'banner_image.image' => 'The banner image is in valid',
                'banner_image.required_if' => 'The banner image is required',
                'banner_image.max' => 'The file size must be less than 2 MB',


            ]);
        $blogPage = LatestNews::firstOrNew();
        if ($request->hasFile('banner_image')) {
            if ($blogPage->banner_image) {
                Storage::disk('public')->delete('uploads/latestNewsPage/banner_image/' . $blogPage->banner_image);
            }
            $image = $request->banner_image;
            $imageUpload = time() . '.' . $request->banner_image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/latestNewsPage/banner_image', $imageUpload, 'public');
            $blogPage->banner_image = $imageUpload;
        }

        $blogPage->banner_title = $request->banner_title;
        $blogPage->banner_description = $request->banner_description;

        if ($blogPage->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect()->route('admin.latestNewsPage.edit');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }
}
