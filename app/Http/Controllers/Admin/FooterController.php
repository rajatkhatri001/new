<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Footer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FooterController extends Controller
{
    public function edit()
    {
        $companyProfile = Footer::firstOrNew();
        return view('Admin.Footer.edit', compact('companyProfile'));
    }

    public function update(Request $request)
    {
        $request->validate([

            'description' => 'required|string',

        ],
        [

            'description.required' => 'The company background description is required',
            'description.string' => 'The company background description is in valid',

        ]);
        $companyProfile = Footer::firstOrNew();

        $companyProfile->description = $request->description;

        if ($companyProfile->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect()->route('admin.footer.edit');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }
}
