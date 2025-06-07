<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FranchiseOpportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FrechiseOpprtunityController extends Controller
{
    public function edit()
    {
        $companyProfile = FranchiseOpportunity::firstOrNew();
        return view('Admin.FranchiseOpportunity.edit', compact('companyProfile'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'button_title' => 'required|string',
            // 'button_link' => 'required|string',
            'title' => 'required|string',
        ],
        [
            'button_title.required' => 'The button title is required',
            'button_title.string' => 'The button title is in valid',
            'title.required' => 'The title is required',
            'title.string' => 'The title is in valid',
            // 'button_link.required' => 'The button link is required',
            // 'button_link.string' => 'The button link is in valid',
        ]);
        $companyProfile = FranchiseOpportunity::firstOrNew();

        $companyProfile->title = $request->title;
        $companyProfile->button_title = $request->button_title;
        // $companyProfile->button_link = $request->button_link;

        if ($companyProfile->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect()->route('admin.frenchise_opportunities.edit');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }
}
