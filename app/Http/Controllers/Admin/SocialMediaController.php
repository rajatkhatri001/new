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
use Redirect;
use DataTables;
use App\Models\SocialMedia;


class SocialMediaController extends Controller
{
    // Get Social Media List Page
    public function socialList(Request $request)
    {
        if ($request->ajax()) {
			$data = SocialMedia::get();
            return DataTables::of($data)
            ->addColumn('image', function ($row) {
                $thumbUrl = asset(Storage::url('app/public/uploads/social/' . $row->image));
                return $thumbUrl;
            })
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        $checkbox = '<div class="custom-control custom-switch switch-primary switch-md ">
                        <input type="checkbox" class="custom-control-input" id="switch-s1-'. $row->id .'" checked="true" onclick="socialStatusOnOff(\'' . $row->id . '\', this)">
                        <label class="custom-control-label" for="switch-s1-'. $row->id .'"></label>
                    </div>';
                    } else {
                        $checkbox = '<div class="custom-control custom-switch switch-primary switch-md ">
                        <input type="checkbox" class="custom-control-input" id="switch-s1-'. $row->id .'" onclick="socialStatusOnOff(\'' . $row->id . '\', this)">
                        <label class="custom-control-label" for="switch-s1-'. $row->id .'"></label>
                    </div>';
                    }
                    return $checkbox;
                })
                ->addColumn('action', function ($row) {
                    $btn['id'] = $row->id;
                    $btn['edit'] = $row->id;
                    $btn['delete'] = $row->id;
                    return View::make('Admin.SocialMedia.action', compact('btn'))->render();
                })
                ->rawColumns(['action', 'status','name'])
                ->make(true);
        }
        return view('Admin.SocialMedia.list');
    }

    // Get Store Social Media Page
    public function add(Request $request, $id = null)
	{
        if ($id) {
            $SocialMedia = SocialMedia::where('id', $id)->first();
            if ($SocialMedia) {
                return view('Admin.SocialMedia.add', compact('SocialMedia'));
            }
        }else {
            return view('Admin.SocialMedia.add');
        }
	}

    // Store New Social Media
    public function store(Request $request){
        $input = $request->all();
        $rules = [
            'name' => 'required|string',
            'image' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ];

        $message = [
            'name.required' => 'The name is required',
            'image.required' => 'Please upload an image file.',
            'image.image' => 'The Image is required.',
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
            $image_path = $image->storeAs('uploads/social', $sectorImageUpload, 'public');
        }
        $data = new SocialMedia;
        $data->name = $request->name;
        $data->image = $sectorImageUpload;
        $data->status = $request->status ? 1 : 0;
        $data->save();

        $request->session()->flash('alert-success', trans('admin_messages.record_add_msg'));
        return redirect('admin/social/social-media-list');
    }

    // Update Social Media
    public function update(Request $request)
    {
        $input = $request->all();
        $dataObj = SocialMedia::where('id', $request->id)->first();

        if ($request->hasFile('image')){
            $oldrImagePath = 'public/social/' . $dataObj->image;
            if (File::exists($oldrImagePath)) {
                File::delete($oldrImagePath);
            }
            $Image = $request->file('image');
            $imageUpload = uniqid() . '.' . $Image->getClientOriginalExtension();
            $path = $Image->storeAs('uploads/social', $imageUpload, 'public');
            $dataObj->image = $imageUpload;
        }
        $dataObj->name = $request->name;
        $dataObj->status = $request->status ? 1 : 0;
		$dataObj->save();
		$request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
		return redirect('admin/social/social-media-list');
    }

    // Remove Social Media
    public function remove(Request $request, $id = 0){
        $data = SocialMedia::findOrFail($id);
		if (!empty($data)) {
            $oldImagePath = $data->image;
			if (!empty($oldImagePath)) {

                $objImagePath = 'public/uploads/social/' . $oldImagePath;
				if (File::exists($objImagePath)) {
                    File::delete($objImagePath);
				}
			}
			$data->delete();
			$request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
		}
		return redirect('admin/social/social-media-list');
    }

    //Social Media Status change
    public function socialStatusChange(Request $request, $id = 0)
    {
       try {
           $data = SocialMedia::where('id', $id)->first();
           $data->status = ($data->status == 0 ? 1 : 0);
           $data->save();
       } catch (Exception $ex) {
           $request->session()->flash('alert-danger', trans('admin_messages.something_went'));
           return redirect('admin/social/social-media-list');
       }
    }
}
