<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProudMember;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Redirect;
use DataTables;

class ProudMembersController extends Controller
{
    public function list(Request $request)
    {

        if ($request->ajax()) {

			$data = ProudMember::get();
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
                ->editColumn('image', function ($row) {
                    $thumbUrl = asset(Storage::url('app/public/uploads/ProudMembers/' . $row->image));
					return $thumbUrl;
				})
                ->addColumn('action', function ($row) {
                    $btn['id'] = $row->id;
                    $btn['edit'] = $row->id;
                    $btn['delete'] = $row->id;
                    return View::make('Admin.ProudMembers.action', compact('btn'))->render();
                })
                ->rawColumns(['action', 'status', 'image'])
                ->make(true);
        }
        return view('Admin.ProudMembers.list');
    }

    public function add()
	{
		return view('Admin.ProudMembers.add');
	}

    public function edit(Request $request, $id)
	{
		$companyProfile = ProudMember::where('id', $id)->first();
		return view('Admin.ProudMembers.add',compact('companyProfile'));
	}

    public function store(Request $request){

        $input = $request->all();

        $rules = [

            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];

        $message = [
            'image.mimes'=>'Please upload only image files of type: PNG, JPG, JPEG, WEBP',
            'image.required' => 'Image field is required',
            'image.max' => 'The file size must be less than 2 MB',
        ];

        $validator = Validator::make($input, $rules, $message);
		if ($validator->fails()) {
			return Redirect::back()
				->withErrors($validator)
				->withInput();
		}

        $img = $request->image;
        if ($img) {
			$sectorImgUpload = time() . '.' . $img->getClientOriginalExtension();
			$path = $img->storeAs('uploads/ProudMembers', $sectorImgUpload, 'public');
		}

        $member = new ProudMember;

        $member->image = $sectorImgUpload;
        $member->status = $request->status ? 1 : 0;
        $member->save();

        $request->session()->flash('alert-success', trans('admin_messages.record_add_msg'));
		return redirect('admin/proud_member/list');
    }

    public function update(Request $request){
        $member = ProudMember::where('id', $request->id)->first();


        if ($request->hasFile('image')){
            $oldImagePath = 'public/ProudMembers/' . $member->image;
				if (File::exists($oldImagePath)) {
					File::delete($oldImagePath);
				}
                $mainImage = $request->file('image');
                $mainImageUpload = time() . '.' . $mainImage->getClientOriginalExtension();
                $path2 = $mainImage->storeAs('uploads/ProudMembers', $mainImageUpload, 'public');
                $member->image = $mainImageUpload;

        }
        $member->status = $request->status ? 1 : 0;
		$member->save();
		$request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
		return redirect('admin/proud_member/list');
    }

    public function statusChange(Request $request, $id = 0)
    {
        try {
            $member = ProudMember::where('id', $id)->first();
            $member->status = ($member->status == 0 ? 1 : 0);
            $member->save();
        } catch (Exception $ex) {
            $request->session()->flash('alert-danger', trans('admin_messages.something_went'));
            return redirect('admin/proud_member/list');
        }
    }

    public function remove(Request $request, $id = 0){
        $division = ProudMember::findOrFail($id);
		if (!empty($division)) {

            $mainImageFileName = $division->image;
			if (!empty($mainImageFileName)) {

                $mainImagePath = 'public/uploads/ProudMembers/' . $mainImageFileName;
				if (File::exists($mainImagePath)) {
                    File::delete($mainImagePath);
				}
			}
			$division->delete();
			$request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
		}
		return redirect('admin/proud_member/list');
    }
}
