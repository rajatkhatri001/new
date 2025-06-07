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
use App\Models\ContactUsForm;
use Redirect;
use DataTables;
use App\Models\ContactUs;
use App\Models\ContactUsPage;
use App\Models\OurDivision;

class ContactUsController extends Controller
{
        //Add or Edit ContactUs banner
        public function editContactUs()
        {
            $ContactUs = ContactUs::firstOrNew();
            return view('Admin.ContactUs.edit', compact('ContactUs'));
        }

        //Update ContactUs banner
        public function update(Request $request)
        {
            $request->validate([
                'banner_image' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
                // 'banner_title' => 'required|string',
                // 'banner_description' => 'required|string',
            ],
            [
                'banner_image.required'=>'The Image is required',
                'banner_image.mimes'=>'Please upload only image files of type: PNG, JPG, JPEG, WEBP',
                'banner_image.max' => 'The file size must be less than 2 MB',

                // 'banner_title.required' => 'The banner title is required',
                // 'banner_title.string' => 'The banner title is in valid',
                // 'banner_description.required' => 'The banner description is required',
                // 'banner_description.string' => 'The banner description is in valid',
            ]);
            $contacBanner = ContactUs::firstOrNew();
            if ($request->hasFile('banner_image')) {
                if ($contacBanner->banner_image) {
                    Storage::disk('public')->delete('uploads/contactus/' . $contacBanner->banner_image);
                }
                $image = $request->banner_image;
                $imageUpload = uniqid() . '.' . $request->banner_image->getClientOriginalExtension();
                $path = $image->storeAs('uploads/contactus', $imageUpload, 'public');
                $contacBanner->banner_image = $imageUpload;
            }

            $contacBanner->banner_title = $request->banner_title;
            $contacBanner->banner_description = $request->banner_description;
            if ($contacBanner->save()) {
                $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
                return redirect()->route('admin.contactus.edit');
            } else {
                $request->session()->flash('alert-error', trans('admin_messages.something_went'));
                return redirect()->back();
            }
        }

        //Add or Edit ContactUs Page
        public function editContactUsPage()
        {
            $ContactUsPage = ContactUsPage::firstOrNew();
            return view('Admin.ContactUs.editpage', compact('ContactUsPage'));
        }

        //Update ContactUs page
        public function updateContactUsPage(Request $request)
        {
            $request->validate([
                'address' => 'required|string',
                'mobile' => 'required|numeric',
                'email' => 'required|string',
                'map_iframe' => 'required|string',
            ],
            [
                'address.required' => 'The Address is required',
                'mobile.required' => 'The Mobile Number is required',
                'email.required' => 'The email is required',
                'map_iframe.required' => 'The Map Iframe is required',
                'map_iframe.string' => 'The Map Iframe is in valid',
            ]);
            $contacPage = ContactUsPage::firstOrNew();
            $contacPage->address = $request->address;
            $contacPage->mobile = $request->mobile;
            $contacPage->email = $request->email;
            $contacPage->map_iframe = $request->map_iframe;
            if ($contacPage->save()) {
                $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
                return redirect()->route('admin.contactuspage.edit');
            } else {
                $request->session()->flash('alert-error', trans('admin_messages.something_went'));
                return redirect()->back();
            }
        }

        function listContactUsPage(Request $request){

            if ($request->ajax()) {

                $data = ContactUsForm::get();
                return DataTables::of($data)
                    ->addColumn('name', function ($row) {
                        return $row->name;
                    })
                    ->addColumn('number', function ($row) {
                        return $row->number;
                    })
                    ->addColumn('email', function ($row) {
                        return $row->email;
                    })
                    
                    
                    ->addColumn('action', function ($row) {
                        $btn['id'] = $row->id;
                        $btn['view'] = $row->id;
                        $btn['delete'] = $row->id;
                        return View::make('Admin.ContactUsForm.action', compact('btn'))->render();
                    })
                    // ->rawColumns(['action', 'status', 'thumbnail'])
                    ->rawColumns(['action', 'name', 'number', 'email'])
                    ->make(true);
            }
            return view('Admin.ContactUsForm.list');
        }

        public function removeContactUsPage(Request $request, $id = 0){
            $mpimage = ContactUsForm::findOrFail($id);
            if (!empty($mpimage)) {
    
                
                $mpimage->delete();
                $request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
            }
            return redirect('admin/contactusform/list');
        }

        public function viewform(Request $request){
            $id = $request->id;

            $contactusformview=ContactUsForm::where('id',$id)->first();

            $divisionid = $contactusformview->division;

            $divisionname = OurDivision::where('id',$divisionid)->first();
            
            return view('Admin.ContactUsForm.view',compact('contactusformview','divisionname'));
        }
}

