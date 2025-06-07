<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DealWithRange;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Redirect;
use DataTables;

class DealWithRangeController extends Controller
{
    public function edit()
    {
        $companyProfile = DealWithRange::firstOrNew();
        return view('Admin.DealWithRange.edit', compact('companyProfile'));
    }

    public function update(Request $request)
    {

        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
        ],
        [
            
            'title.required' => 'Please enter a title.',
            'description.required' => 'Please enter a description.',
        ]);

            
        $companyProfile = DealWithRange::firstOrNew();

        $companyProfile->title = $request->title;
        $companyProfile->description = $request->description;

        if ($companyProfile->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect()->route('admin.deal_with_range.edit');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }
}
