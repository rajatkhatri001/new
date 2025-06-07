<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventPageController extends Controller
{
    //
    public function edit()
    {
        $eventPage = EventPage::firstOrNew();
        return view('Admin.EventPage.edit', compact('eventPage', ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'banner_image' => 'required_if:old_banner_image,==,null|image|mimes:jpeg,png,jpg,webp|max:2048',
            // 'banner_title' => 'required|string',
            // 'banner_description' => 'required|string',
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

            ]);
        $eventPage = EventPage::firstOrNew();
        if ($request->hasFile('banner_image')) {
            if ($eventPage->banner_image) {
                Storage::disk('public')->delete('uploads/eventPage/banner_image/' . $eventPage->banner_image);
            }
            $image = $request->banner_image;
            $imageUpload = time() . '.' . $request->banner_image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/eventPage/banner_image', $imageUpload, 'public');
            $eventPage->banner_image = $imageUpload;
        }

        $eventPage->banner_title = $request->banner_title;
        $eventPage->banner_description = $request->banner_description;

        if ($eventPage->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect()->route('admin.eventpage.edit');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }
}
