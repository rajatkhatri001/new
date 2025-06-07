<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogPageController extends Controller
{
    //
    public function edit()
    {
        $blogPage = BlogPage::firstOrNew();
        return view('Admin.BlogPage.edit', compact('blogPage', ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'banner_image' => 'required_if:old_banner_image,==,null|image|mimes:jpeg,png,jpg,webp|max:2048',
            // 'banner_title' => 'required|string',
            // 'banner_description' => 'required|string',
            'blog_list_banner_image' => 'required_if:old_blog_list_banner_image,==,null|image|mimes:jpeg,png,jpg,webp|max:2048',
            // 'blog_list_banner_title' => 'required|string',
            // 'blog_list_banner_description' => 'required|string',
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
                'blog_list_banner_image.mimes' => 'Please upload only blog list banner image files of type: PNG, JPG, JPEG, WEBP',
                'blog_list_banner_image.image' => 'The blog list banner image is in valid',
                'blog_list_banner_image.required_if' => 'The blog list banner image is required',
                'blog_list_banner_image.max' => 'The file size must be less than 2 MB',
                // 'blog_list_banner_title.required' => 'The blog list banner title is required',
                // 'blog_list_banner_title.string' => 'The blog list banner title is in valid',
                // 'blog_list_banner_description.required' => 'The blog list banner description is required',
                // 'blog_list_banner_description.string' => 'The blog list banner description is in valid',

            ]);
        $blogPage = BlogPage::firstOrNew();
        if ($request->hasFile('banner_image')) {
            if ($blogPage->banner_image) {
                Storage::disk('public')->delete('uploads/blogpage/banner_image/' . $blogPage->banner_image);
            }
            $image = $request->banner_image;
            $imageUpload = time() . '.' . $request->banner_image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/blogpage/banner_image', $imageUpload, 'public');
            $blogPage->banner_image = $imageUpload;
        }
        if ($request->hasFile('blog_list_banner_image')) {
            if ($blogPage->blog_list_banner_image) {
                Storage::disk('public')->delete('uploads/blogpage/blog_list_banner_image/' . $blogPage->blog_list_banner_image);
            }
            $image = $request->blog_list_banner_image;
            $imageUpload = time() . '.' . $request->blog_list_banner_image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/blogpage/blog_list_banner_image', $imageUpload, 'public');
            $blogPage->blog_list_banner_image = $imageUpload;
        }

        $blogPage->banner_title = $request->banner_title;
        $blogPage->banner_description = $request->banner_description;
        $blogPage->blog_list_banner_title = $request->blog_list_banner_title;
        $blogPage->blog_list_banner_description = $request->blog_list_banner_description;

        if ($blogPage->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect()->route('admin.blogpage.edit');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }
}
