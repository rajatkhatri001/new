<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ManageAwards;
use App\Models\ManageAwardsBanner;
use App\Models\ManageCerificate;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Redirect;
use DataTables;



class AwardsCertificate extends Controller
{
    //Add or Edit Awards banner
    public function editBanner()
    {
        $manageAwardsBanner = ManageAwardsBanner::firstOrNew();
        return view('Admin.ManageAwardsBanner.edit', compact('manageAwardsBanner'));
    }

    //Update Awards banner
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
        $manageAwardsBanner = ManageAwardsBanner::firstOrNew();
        if ($request->hasFile('banner_image')) {
            if ($manageAwardsBanner->banner_image) {
                Storage::disk('public')->delete('uploads/awards/' . $manageAwardsBanner->banner_image);
            }
            $image = $request->banner_image;
            $imageUpload = uniqid() . '.' . $request->banner_image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/awards', $imageUpload, 'public');
            $manageAwardsBanner->banner_image = $imageUpload;
        }

        $manageAwardsBanner->banner_title = $request->banner_title;
        $manageAwardsBanner->banner_description = $request->banner_description;
        if ($manageAwardsBanner->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect()->route('admin.manageAwards.edit');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    // Get Awards List Page
    public function awardsList(Request $request)
    {
        if ($request->ajax()) {
			$data = ManageAwards::get();
            return DataTables::of($data)
                ->addColumn('title', function ($row) {
                    return $row->title;
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        $checkbox = '<div class="custom-control custom-switch switch-primary switch-md ">
                        <input type="checkbox" class="custom-control-input" id="switch-s1-'. $row->id .'" checked="true" onclick="awardstatusOnOff(\'' . $row->id . '\', this)">
                        <label class="custom-control-label" for="switch-s1-'. $row->id .'"></label>
                    </div>';
                    } else {
                        $checkbox = '<div class="custom-control custom-switch switch-primary switch-md ">
                        <input type="checkbox" class="custom-control-input" id="switch-s1-'. $row->id .'" onclick="awardstatusOnOff(\'' . $row->id . '\', this)">
                        <label class="custom-control-label" for="switch-s1-'. $row->id .'"></label>
                    </div>';
                    }
                    return $checkbox;
                })
                ->addColumn('action', function ($row) {
                    $btn['id'] = $row->id;
                    $btn['edit'] = $row->id;
                    $btn['delete'] = $row->id;
                    return View::make('Admin.ManageAwardsBanner.action', compact('btn'))->render();
                })
                ->rawColumns(['action', 'status','title'])
                ->make(true);
        }
        return view('Admin.ManageAwardsBanner.awardsList');
    }

    // Get Store Awards Page
    public function addAwards(Request $request, $id = null)
	{
        if ($id) {
            $ManageAwards = ManageAwards::where('id', $id)->first();
            if ($ManageAwards) {
                return view('Admin.ManageAwardsBanner.addAwards', compact('ManageAwards'));
            }
        }else {
            return view('Admin.ManageAwardsBanner.addAwards');
        }
	}

    // Store New Awards
    public function storeAwards(Request $request){
        $input = $request->all();
        $rules = [
            'title' => 'required|string',
            'description' => 'required|string',
            'image' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ];

        $message = [
            'title.required' => 'The title is required',
            'description.required' => 'The description is required',
            'image.required' => 'Please upload an image file.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'Please upload only image files of type: PNG, JPG, JPEG, WEBP.',
            'image.max' => 'The file size must be less than 2 MB',
        ];

        $validator = Validator::make($input, $rules, $message);
        if ($validator->fails()) {
            return Redirect::back()
                ->withErrors($validator)
                ->withInput();
        }

        $image = $request->image;
        if ($image) {
            $sectorImageUpload = uniqid() . '.' . $image->getClientOriginalExtension();
            $image_path = $image->storeAs('uploads/awards', $sectorImageUpload, 'public');
        }
        $data = new ManageAwards;
        $data->title = $request->title;
        $data->image = $sectorImageUpload;
        $data->description = $request->description;
        $data->status = $request->status ? 1 : 0;
        $data->save();

        $request->session()->flash('alert-success', trans('admin_messages.record_add_msg'));
        return redirect('admin/manage-awards-certificate-awards/awards-list');
    }

    // Update Awards
    public function updateAwards(Request $request)
    {
        $input = $request->all();
        $dataObj = ManageAwards::where('id', $request->id)->first();

        if ($request->hasFile('image')){
            $oldrImagePath = 'public/awards/' . $dataObj->image;
            if (File::exists($oldrImagePath)) {
                File::delete($oldrImagePath);
            }
            $Image = $request->file('image');
            $imageUpload = uniqid() . '.' . $Image->getClientOriginalExtension();
            $path = $Image->storeAs('uploads/awards', $imageUpload, 'public');
            $dataObj->image = $imageUpload;
        }
        $dataObj->title = $request->title;
        $dataObj->description = $request->description;
        $dataObj->status = $request->status ? 1 : 0;
		$dataObj->save();
		$request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
		return redirect('admin/manage-awards-certificate-awards/awards-list');
    }

    // Remove Awards
    public function removeAwards(Request $request, $id = 0){
        $data = ManageAwards::findOrFail($id);
		if (!empty($data)) {
            $oldImagePath = $data->image;
			if (!empty($oldImagePath)) {

                $objImagePath = 'public/uploads/awards/' . $oldImagePath;
				if (File::exists($objImagePath)) {
                    File::delete($objImagePath);
				}
			}
			$data->delete();
			$request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
		}
		return redirect('admin/manage-awards-certificate-awards/awards-list');
    }
    //Award Status change
    public function awardStatusChange(Request $request, $id = 0)
    {
       try {
           $data = ManageAwards::where('id', $id)->first();
           $data->status = ($data->status == 0 ? 1 : 0);
           $data->save();
       } catch (Exception $ex) {
           $request->session()->flash('alert-danger', trans('admin_messages.something_went'));
           return redirect('admin/manage-awards-certificate-awards/awards-list');
       }
    }

    // Get Certificate List Page
    public function certificateList(Request $request)
    {
        if ($request->ajax()) {
			$data = ManageCerificate::get();
            return DataTables::of($data)
                ->addColumn('image', function ($row) {
                    if ($row->image != "") {
                        // $certificateImg = '<img src="' . $row->image . '" alt="Certificate Image" width="100" height="100">';
                        $certificateImg = asset('storage/app/public/uploads/certificate') . '/' . $row->image;
                    } else {
                        $certificateImg = '';
                    }
                    return $certificateImg;
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
                    return View::make('Admin.ManageAwardsBanner.certificateAction', compact('btn'))->render();
                })
                ->rawColumns(['action', 'status','title'])
                ->make(true);
        }
        return view('Admin.ManageAwardsBanner.certificateList');
    }

    // Get Store Certificate Page
    public function addCertificate(Request $request, $id = null)
	{
        if ($id) {
            $ManageCerificate = ManageCerificate::where('id', $id)->first();
            if ($ManageCerificate) {
                return view('Admin.ManageAwardsBanner.addCertificate', compact('ManageCerificate'));
            }
        }else {
            return view('Admin.ManageAwardsBanner.addCertificate');
        }
	}


    // Store New Cerificate
    public function storeCerificate(Request $request){
        $input = $request->all();
        $rules = [
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'name' => 'required|string',
        ];

        $messages = [
            'name.required' => 'The name is required',
            'image.required' => 'Please upload an image file.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'Please upload only image files of type: PNG, JPG, JPEG, WEBP.',
            'image.max' => 'The file size must be less than 2 MB',
        ];

        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $image = $request->image;
        if ($image) {
            $sectorImageUpload = uniqid() . '.' . $image->getClientOriginalExtension();
            $image_path = $image->storeAs('uploads/certificate', $sectorImageUpload, 'public');
        }
        $data = new ManageCerificate;
        $data->image = $sectorImageUpload;
        $data->name = $request->name;
        $data->status = $request->status ? 1 : 0;
        $data->save();

        $request->session()->flash('alert-success', trans('admin_messages.record_add_msg'));
        return redirect('admin/manage-awards-certificate/certificate-list');
    }

     // Update Certificate
     public function updateCerificate(Request $request)
     {
         $input = $request->all();
         $dataObj = ManageCerificate::where('id', $request->id)->first();

         if ($request->hasFile('image')){
             $oldrImagePath = 'public/certificate/' . $dataObj->image;
             if (File::exists($oldrImagePath)) {
                 File::delete($oldrImagePath);
             }
             $Image = $request->file('image');
             $imageUpload = uniqid() . '.' . $Image->getClientOriginalExtension();
             $path = $Image->storeAs('uploads/certificate', $imageUpload, 'public');
             $dataObj->image = $imageUpload;
         }
         $dataObj->name = $request->name;
         $dataObj->status = $request->status ? 1 : 0;
         $dataObj->save();
         $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
         return redirect('admin/manage-awards-certificate/certificate-list');
     }

     // Remove Certificate
     public function removeCertificate(Request $request, $id = 0){
         $data = ManageCerificate::findOrFail($id);
         if (!empty($data)) {
             $oldImagePath = $data->image;
             if (!empty($oldImagePath)) {

                 $objImagePath = 'public/uploads/certificate/' . $oldImagePath;
                 if (File::exists($objImagePath)) {
                     File::delete($objImagePath);
                 }
             }
             $data->delete();
             $request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
         }
         return redirect('admin/manage-awards-certificate/certificate-list');
     }
     //Certificate Status change
    public function certificateStatusChange(Request $request, $id = 0)
    {
       try {
           $data = ManageCerificate::where('id', $id)->first();
           $data->status = ($data->status == 0 ? 1 : 0);
           $data->save();
       } catch (Exception $ex) {
           $request->session()->flash('alert-danger', trans('admin_messages.something_went'));
           return redirect('admin/manage-awards-certificate/certificate-list');
       }
    }









}
