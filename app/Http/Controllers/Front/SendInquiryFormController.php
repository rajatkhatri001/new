<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SendInquiryForm;

class SendInquiryFormController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $inquiry = SendInquiryForm::create([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        //return response()->json(['success' => 'Inquiry submitted successfully!']);
        if ($inquiry->save()) {
            $request->session()->flash('alert-success', 'Inquiry submitted successfully!');
            return redirect()->back();
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }
}
