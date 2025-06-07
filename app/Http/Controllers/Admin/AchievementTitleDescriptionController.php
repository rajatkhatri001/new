<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AchievementTitleDescription;


class AchievementTitleDescriptionController extends Controller
{
    public function edit()
    {
        $companyProfile = AchievementTitleDescription::firstOrNew();
        return view('Admin.AchievementTitleDescription.edit', compact('companyProfile'));
    }

    public function update(Request $request)
    {
        $request->validate([

            'description' => 'required|string',

            'title' => 'required|string',

        ],
        [

            'description.required' => 'The description is required',
            'description.string' => 'The description is in valid',

            'title.required' => 'The title is required',
            'title.string' => 'The title is in valid',

        ]);

        $companyProfile = AchievementTitleDescription::firstOrNew();
        $companyProfile->description = $request->description;

        $companyProfile->title = $request->title;

        if ($companyProfile->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect()->route('admin.achievement_title_description.edit');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }
}
