<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ManufacturingPlant;
use App\Models\ManufacturingPlantList;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Redirect;
use DataTables;

class ManufacturingPlantController extends Controller
{
    public function ManufacturingPlantList(Request $request)
    {
        if ($request->ajax()) {
			$data = ManufacturingPlantList::get();
            return DataTables::of($data)
                ->addColumn('title', function ($row) {
                    return $row->title;
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
                    return View::make('Admin.ManufacturingPlant.action', compact('btn'))->render();
                })
                ->rawColumns(['action', 'status','title'])
                ->make(true);
        }
        return view('Admin.ManufacturingPlant.list');
    }

    public function add(Request $request, $id = null)
	{
        if ($id) {
            $blogObjs = ManufacturingPlantList::where('id', $id)->first();
            if ($blogObjs) {
                return view('Admin.ManufacturingPlant.add', compact('blogObjs'));
            }
        }else {
            return view('Admin.ManufacturingPlant.add');
        }
	}

    public function edit(Request $request)
	{
		$ManufacturingData = ManufacturingPlant::firstOrNew();
		return view('Admin.ManufacturingPlant.edit',compact('ManufacturingData'));

	}

    public function AddOrUpdate(Request $request){
        $input = $request->all();
        $request->validate([
            'content_title' => 'required',
            'banner_title' => 'required|string',
            'objective_title' => 'required|string',
            'banner_image' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'objective_image' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'banner_description' => 'required|string',
            'objective_description' => 'required|string',
        ],
        [
            'content_title' => trans('validation.content_title_required'),
            'banner_title.required' => 'The banner title is required',
            'objective_title.string' => 'The objective title is in valid',
            'banner_image.required'=>'The Image is required',
            'banner_image.mimes'=>'Please upload only image files of type: PNG, JPG, JPEG, WEBP',
            'banner_image.max' => 'The file size must be less than 2 MB',
            'objective_image.required'=>'The Image is required',
            'objective_image.mimes'=>'Please upload only image files of type: PNG, JPG, JPEG, WEBP',
            'objective_image.max' => 'The file size must be less than 2 MB',
            'banner_description.required' => 'The banner description is required',
            'objective_description.string' => 'The objective description is in valid',
        ]);
        $ManufacturingProfile = ManufacturingPlant::firstOrNew();
        if ($request->hasFile('banner_image')) {
            if ($ManufacturingProfile->banner_image) {
                Storage::disk('public')->delete('uploads/ManufacturingPlant/' . $ManufacturingProfile->banner_image);
            }
            $bannerImage = $request->banner_image;
            $bannerImageUpload = uniqid() . '.' . $request->banner_image->getClientOriginalExtension();
            $bannerpath = $bannerImage->storeAs('uploads/ManufacturingPlant', $bannerImageUpload, 'public');
            $ManufacturingProfile->banner_image = $bannerImageUpload;
        }
        if ($request->hasFile('objective_image')) {
            if ($ManufacturingProfile->objective_image) {
                Storage::disk('public')->delete('uploads/ManufacturingPlant/' . $ManufacturingProfile->objective_image);
            }
            $objectiveImage = $request->objective_image;
            $imageUpload = uniqid() . '.' . $request->objective_image->getClientOriginalExtension();
            $objectivepath = $objectiveImage->storeAs('uploads/ManufacturingPlant', $imageUpload, 'public');
            $ManufacturingProfile->objective_image = $imageUpload;
        }
        $ManufacturingProfile->content_title = $request->content_title;
        $ManufacturingProfile->banner_title = $request->banner_title;
        $ManufacturingProfile->banner_description = $request->banner_description;
        $ManufacturingProfile->objective_title = $request->objective_title;
        $ManufacturingProfile->objective_description = $request->objective_description;
        if ($ManufacturingProfile->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect()->route('admin.manageManufacturing.edit');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    public function updateList(Request $request)
    {
        $input = $request->all();

        $dataObj = ManufacturingPlantList::where('id', $request->id)->first();

        if ($request->hasFile('image')){
            $oldrImagePath = 'public/ManufacturingPlant/' . $dataObj->image;
            if (File::exists($oldrImagePath)) {
                File::delete($oldrImagePath);
            }
            $Image = $request->file('image');
            $imageUpload = uniqid() . '.' . $Image->getClientOriginalExtension();
            $path = $Image->storeAs('uploads/ManufacturingPlant', $imageUpload, 'public');
            $dataObj->image = $imageUpload;
        }
        $dataObj->title = $request->title;
        $dataObj->description = $request->description;
        $dataObj->status = $request->status ? 1 : 0;
		$dataObj->save();
		$request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
		return redirect('admin/manufacturing-plant/list');
    }

    public function statusChange(Request $request, $id = 0)
    {
        try {
            $data = ManufacturingPlantList::where('id', $id)->first();
            $data->status = ($data->status == 0 ? 1 : 0);
            $data->save();
        } catch (Exception $ex) {
            $request->session()->flash('alert-danger', trans('admin_messages.something_went'));
            return redirect('admin/manufacturing-plant/list');
        }
    }

    public function remove(Request $request, $id = 0){
        $data = ManufacturingPlantList::findOrFail($id);
		if (!empty($data)) {
            $oldImagePath = $data->image;
			if (!empty($oldImagePath)) {

                $objImagePath = 'public/uploads/ManufacturingPlant/' . $oldImagePath;
				if (File::exists($objImagePath)) {
                    File::delete($objImagePath);
				}
			}
			$data->delete();
			$request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
		}
		return redirect('admin/manufacturing-plant/list');
    }

    public function storeManufacturingPlant(Request $request){
        $input = $request->all();
        $rules = [
            'title' => 'required|string',
            'description' => 'required|string',
            'image' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ];

        $message = [
            'title.required' => 'The title is required',
            'description.required' => 'The description is required',
            'image.required' => 'The Image is required',
            'image.mimes'=>'Please upload only image files of type: PNG, JPG, JPEG, WEBP',
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
            $image_path = $image->storeAs('uploads/ManufacturingPlant', $sectorImageUpload, 'public');
        }
        $data = new ManufacturingPlantList;
        $data->title = $request->title;
        $data->image = $sectorImageUpload;
        $data->description = $request->description;
        $data->save();

        $request->session()->flash('alert-success', trans('admin_messages.record_add_msg'));
        return redirect('admin/manufacturing-plant/list');
    }


}
