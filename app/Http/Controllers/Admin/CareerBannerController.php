<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CareerBanner;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Models\JoinUs;
use App\Models\CurrentOpportunites;
use Redirect;
use DataTables;

class CareerBannerController extends Controller
{
        //Add or Edit Career banner
        public function editCareer()
        {
            $careerBanner = CareerBanner::firstOrNew();
            return view('Admin.Career.edit', compact('careerBanner'));
        }

        //Update Career banner
        public function update(Request $request)
        {
            $request->validate([
                'banner_image' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
                // 'banner_title' => 'required|string',
                // 'banner_description' => 'required|string',
            ],
            [
                'banner_image.mimes'=>'Please upload only image files of type: PNG, JPG, JPEG, WEBP',
                'banner_image.max' => 'The file size must be less than 2 MB',
                // 'banner_title.required' => 'The banner title is required',
                // 'banner_title.string' => 'The banner title is in valid',
                // 'banner_description.required' => 'The banner description is required',
                // 'banner_description.string' => 'The banner description is in valid',
            ]);
            $careerBanner = CareerBanner::firstOrNew();
            if ($request->hasFile('banner_image')) {
                if ($careerBanner->banner_image) {
                    Storage::disk('public')->delete('uploads/career/' . $careerBanner->banner_image);
                }
                $image = $request->banner_image;
                $imageUpload = uniqid() . '.' . $request->banner_image->getClientOriginalExtension();
                $path = $image->storeAs('uploads/career', $imageUpload, 'public');
                $careerBanner->banner_image = $imageUpload;
            }

            $careerBanner->banner_title = $request->banner_title;
            $careerBanner->banner_description = $request->banner_description;
            if ($careerBanner->save()) {
                $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
                return redirect()->route('admin.career.edit');
            } else {
                $request->session()->flash('alert-error', trans('admin_messages.something_went'));
                return redirect()->back();
            }
        }

        //Why Join Us Listing
        public function join_us(Request $request)
        {
            if ($request->ajax()) {
                $data = JoinUs::get();
                return DataTables::of($data)
                    ->addColumn('title', function ($row) {
                        return $row->title_1;
                    })
                    ->addColumn('status', function ($row) {
                        if ($row->status == 1) {
                            $checkbox = '<div class="custom-control custom-switch switch-primary switch-md ">
                            <input type="checkbox" class="custom-control-input" id="switch-s1-'. $row->id .'" checked="true" onclick="statusOnOff(\'' . $row->id . '\', this)">
                            <label class="custom-control-label" for="switch-s1-'. $row->id .'"></label>
                        </div>';
                        } else {
                            $checkbox = '<div class="custom-control custom-switch switch-primary switch-md ">
                            <input type="checkbox" class="custom-control-input" id="switch-s1-'. $row->id .'" onclick="statusOnOff(\'' . $row->id . '\', this)">
                            <label class="custom-control-label" for="switch-s1-'. $row->id .'"></label>
                        </div>';
                        }
                        return $checkbox;
                    })
                    ->addColumn('action', function ($row) {
                        $btn['id'] = $row->id;
                        $btn['edit'] = $row->id;
                        $btn['delete'] = $row->id;
                        return View::make('Admin.Career.action', compact('btn'))->render();
                    })
                    ->rawColumns(['action', 'title', 'status'])
                    ->make(true);
            }
            return view('Admin.Career.whyJoinUs');
        }

        // Get Store WhyUs Page
        public function addjoinUs(Request $request, $id = null)
        {
            if ($id) {
                $joinUsData = JoinUs::where('id', $id)->first();
                if ($joinUsData) {
                    return view('Admin.Career.addjoinUs', compact('joinUsData'));
                }
            }else {
                return view('Admin.Career.addjoinUs');
            }
        }

        // Store New WhyUs
        public function storeWhyUs(Request $request){
            $input = $request->all();
            // echo "<pre>"; print_r($input); die();
            $rules = [
                'image1' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
                'image2' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
                'title_1' => 'required',
                'title_2' => 'required',
                'title_3' => 'required',
                'title_4' => 'required',
                'description_1' => 'required|string',
                'description_2' => 'required|string',
                'description_3' => 'required|string',
                'description_4' => 'required|string',
            ];
            $message = [
                'image1.mimes'=>'Please upload only image files of type: PNG, JPG, JPEG, WEBP',
                'image2.mimes'=>'Please upload only image files of type: PNG, JPG, JPEG, WEBP',
                'image1.max' => 'The file size must be less than 2 MB',
                'image2.max' => 'The file size must be less than 2 MB',
                'title_1' => 'The Title is required',
                'title_2' => 'The Title is required',
                'title_3' => 'The Title is required',
                'title_4' => 'The Title is required',
                'description_1.required' => 'The banner description is required',
                'description_2.required' => 'The banner description is required',
                'description_3.required' => 'The banner description is required',
                'description_4.required' => 'The banner description is required',
            ];

            $validator = Validator::make($input, $rules, $message);
            if ($validator->fails()) {
                return Redirect::back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $JoinUs = New JoinUs;
            if ($request->hasFile('image1')) {
                $bannerImage = $request->image1;
                $Image1Upload = uniqid() . '.' . $request->image1->getClientOriginalExtension();
                $bannerpath = $bannerImage->storeAs('uploads/JoinUs', $Image1Upload, 'public');
                $JoinUs->image1 = $Image1Upload;
            }
            if ($request->hasFile('image2')) {
                $objectiveImage = $request->image2;
                $Image2Upload = uniqid() . '.' . $request->image2->getClientOriginalExtension();
                $objectivepath = $objectiveImage->storeAs('uploads/JoinUs', $Image2Upload, 'public');
                $JoinUs->image2 = $Image2Upload;
            }

            $JoinUs->title_1 = $request->title_1;
            $JoinUs->title_2 = $request->title_2;
            $JoinUs->title_3 = $request->title_3;
            $JoinUs->title_4 = $request->title_4;
            $JoinUs->description_1 = $request->description_1;
            $JoinUs->description_2 = $request->description_2;
            $JoinUs->description_3 = $request->description_3;
            $JoinUs->description_4 = $request->description_4;
            $JoinUs->status = $request->status ? 1 : 0;
            $JoinUs->save();

            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect('admin/joinUs');

        }

        // Update Why Us
        public function updateWhyUs(Request $request)
        {
            $input = $request->all();
            $dataObj = JoinUs::where('id', $request->id)->first();

            if ($request->hasFile('image1')){
                $oldrImagePath1 = 'public/JoinUs/' . $dataObj->image1;
                if (File::exists($oldrImagePath1)) {
                    File::delete($oldrImagePath1);
                }
                $Image = $request->file('image1');
                $imageUpload1 = uniqid() . '.' . $Image->getClientOriginalExtension();
                $path = $Image->storeAs('uploads/JoinUs', $imageUpload1, 'public');
                $dataObj->image1 = $imageUpload1;
            }
            if ($request->hasFile('image2')){
                $oldrImagePath2 = 'public/JoinUs/' . $dataObj->image2;
                if (File::exists($oldrImagePath2)) {
                    File::delete($oldrImagePath2);
                }
                $Image = $request->file('image2');
                $imageUpload2 = uniqid() . '.' . $Image->getClientOriginalExtension();
                $path = $Image->storeAs('uploads/JoinUs', $imageUpload2, 'public');
                $dataObj->image2 = $imageUpload2;
            }

            $dataObj->title_1 = $request->title_1;
            $dataObj->title_2 = $request->title_2;
            $dataObj->title_3 = $request->title_3;
            $dataObj->title_4 = $request->title_4;
            $dataObj->description_1 = $request->description_1;
            $dataObj->description_2 = $request->description_2;
            $dataObj->description_3 = $request->description_3;
            $dataObj->description_4 = $request->description_4;
            $dataObj->status = $request->status ? 1 : 0;
            $dataObj->save();
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect('admin/joinUs');
        }

        // Remove WhyUs
        public function removeWhyUs(Request $request, $id = 0){
            $data = JoinUs::findOrFail($id);
            if (!empty($data)) {
                $oldImage1 = $data->image1;
                if (!empty($oldImage1)) {

                    $objImage1 = 'public/uploads/JoinUs/' . $oldImage1;
                    if (File::exists($objImage1)) {
                        File::delete($objImage1);
                    }
                }
                $oldImage2 = $data->image2;
                if (!empty($oldImage2)) {

                    $objImage2 = 'public/uploads/JoinUs/' . $oldImage2;
                    if (File::exists($objImage2)) {
                        File::delete($objImage2);
                    }
                }
                $data->delete();
                $request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
            }
            return redirect('admin/joinUs');
        }
        // Wyh Us Status Change
        public function WhyUsStatus(Request $request, $id = 0)
        {
            try {
                $blog = JoinUs::where('id', $id)->first();
                $blog->status = ($blog->status == 0 ? 1 : 0);
                $blog->save();
            } catch (Exception $ex) {
                $request->session()->flash('alert-danger', trans('admin_messages.something_went'));
                return redirect('admin/joinUs');
            }
        }

        //Current Opportunites Listing
        public function current_Opportunites(Request $request)
        {
            if ($request->ajax()) {
                $data = CurrentOpportunites::get();
                // echo"<pre>"; print_r($data); die();
                return DataTables::of($data)
                    ->addColumn('status', function ($row) {
                        if ($row->status == 1) {
                            $checkbox = '<div class="custom-control custom-switch switch-primary switch-md ">
                            <input type="checkbox" class="custom-control-input" id="switch-s1-'. $row->id .'" checked="true" onclick="statusOnOff(\'' . $row->id . '\', this)">
                            <label class="custom-control-label" for="switch-s1-'. $row->id .'"></label>
                        </div>';
                        } else {
                            $checkbox = '<div class="custom-control custom-switch switch-primary switch-md ">
                            <input type="checkbox" class="custom-control-input" id="switch-s1-'. $row->id .'" onclick="statusOnOff(\'' . $row->id . '\', this)">
                            <label class="custom-control-label" for="switch-s1-'. $row->id .'"></label>
                        </div>';
                        }
                        return $checkbox;
                    })
                    ->addColumn('name', function ($row) {
                        // print_r($row->name); exit;
                        return $row->name;
                    })
                    ->addColumn('action', function ($row) {
                        $btn['id'] = $row->id;
                        $btn['edit'] = $row->id;
                        $btn['delete'] = $row->id;
                        return View::make('Admin.Career.opportunitesAction', compact('btn'))->render();
                    })
                    ->rawColumns(['action', 'status','name'])
                    ->make(true);
            }
            return view('Admin.Career.opportunites');
        }

        // Get Add Opportunite page
        public function addOpportunites(Request $request, $id = null)
        {
            // echo"innn"; die();
            if ($id) {
                $CurrentOpportunites = CurrentOpportunites::where('id', $id)->first();
                if ($CurrentOpportunites) {
                    return view('Admin.Career.addOpportunites', compact('CurrentOpportunites'));
                }
            }else {
                return view('Admin.Career.addOpportunites');
            }
        }

        // Store New Opportunite
        public function storeOpportunites(Request $request){
            $input = $request->all();
            $rules = [
                'name' => 'required|string',
                'location' => 'required|string',
                'description' => 'required|string',
            ];

            $messages = [
                'name.required' => 'The name is required',
                'location.required' => 'The location is required',
                'description.required' => 'The description is required',
            ];

            $validator = Validator::make($input, $rules, $messages);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $data = new CurrentOpportunites;
            $data->name = $request->name;
            $data->location = $request->location;
            $data->description = $request->description;
            $data->status = $request->status ? 1 : 0;
            $data->save();

            $request->session()->flash('alert-success', trans('admin_messages.record_add_msg'));
            return redirect('admin/opportunites');
        }

        //Update Opportunites
        public function updateOpportunites(Request $request)
        {
            $input = $request->all();
            $dataObj = CurrentOpportunites::where('id', $request->id)->first();

            $dataObj->name = $request->name;
            $dataObj->location = $request->location;
            $dataObj->description = $request->description;
            $dataObj->status = $request->status ? 1 : 0;
            $dataObj->save();
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect('admin/opportunites');
        }

        public function opportunitesStatusChange(Request $request, $id = 0)
        {
            try {
                // echo $id; die();
                $blog = CurrentOpportunites::where('id', $id)->first();
                $blog->status = ($blog->status == 0 ? 1 : 0);
                $blog->save();
            } catch (Exception $ex) {
                $request->session()->flash('alert-danger', trans('admin_messages.something_went'));
                return redirect('admin/opportunites');
            }
        }
         // Remove Opportunite
        public function removeOpportunite(Request $request, $id = 0){
            $data = CurrentOpportunites::findOrFail($id);
            if (!empty($data)) {
                $data->delete();
                $request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
            }
            return redirect('admin/opportunites');
        }


}
