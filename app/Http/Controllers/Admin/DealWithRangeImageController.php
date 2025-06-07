<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DealWithRangeimage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Redirect;
use DataTables;

class DealWithRangeImageController extends Controller
{
    public function list(Request $request)
    {
        // echo 111;exit;
        if ($request->ajax()) {

			$data = DealWithRangeimage::get();
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
                ->addColumn('title', function ($row) {
                    return $row->title;
                })
                ->addColumn('action', function ($row) {
                    $btn['id'] = $row->id;
                    $btn['edit'] = $row->id;
                    $btn['delete'] = $row->id;
                    return View::make('Admin.DealWithRangeImage.action', compact('btn'))->render();
                })
                // ->rawColumns(['action', 'status', 'thumbnail'])
                ->rawColumns(['action', 'status','title'])

                ->make(true);
        }
        return view('Admin.DealWithRangeImage.list');
    }

    public function add()
	{
        $data=DealWithRangeimage::where('status', '1')
                           ->get(['title']);
		return view('Admin.DealWithRangeImage.add',compact('data'));
	}

    public function edit(Request $request, $id)
	{
		$dealwithrangeimage = DealWithRangeimage::where('id', $id)->first();
         
		return view('Admin.DealWithRangeImage.add',compact('dealwithrangeimage'));
	}

    public function store(Request $request){

        $input = $request->all();

        $rules = [
            'image' => 'required_if:old_image,==,null|image|mimes:jpeg,png,jpg,webp|max:2048',
            'title' => 'required|string',
        ];

        $message = [
            'image.required' => 'Please upload an image.',
            'image.required_if' => 'Please upload an image.',
            'image.max' => 'The file size must be less than 2 MB',
            'title.required' => 'Please enter a title.',
        ];

        $validator = Validator::make($input, $rules, $message);
		if ($validator->fails()) {
			return Redirect::back()
				->withErrors($validator)
				->withInput();
		}
        $division = new DealWithRangeimage;

        if ($request->hasFile('image')) {
            if ($division->image) {
                Storage::disk('public')->delete('uploads/dealwithrangeimage/' . $companyPrdivisionofile->image);
            }
            $image = $request->image;
            $imageUpload = time() . '.' . $request->image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/dealwithrangeimage', $imageUpload, 'public');
            $division->image = $imageUpload;
        }

        
        $division->title = $request->title;
        $division->status = $request->status ? 1 : 0;
        $division->save();

        $request->session()->flash('alert-success', trans('admin_messages.record_add_msg'));
		return redirect('admin/deal_with_range_image/list');
    }

    public function update(Request $request){
        $division = DealWithRangeimage::where('id', $request->id)->first();

        if ($request->hasFile('image')) {
            if ($division->image) {
                Storage::disk('public')->delete('uploads/dealwithrangeimage/' . $division->image);
            }
            $image = $request->image;
            $imageUpload = time() . '.' . $request->image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/dealwithrangeimage', $imageUpload, 'public');
            $division->image = $imageUpload;
        }

        $division->title = $request->title;
        $division->status = $request->status ? 1 : 0;
		$division->save();
		$request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
		return redirect('admin/deal_with_range_image/list');
    }

    public function statusChange(Request $request, $id = 0)
    {
        try {
            $division = DealWithRangeimage::where('id', $id)->first();
            $division->status = ($division->status == 0 ? 1 : 0);
            $division->save();
        } catch (Exception $ex) {
            $request->session()->flash('alert-danger', trans('admin_messages.something_went'));
            return redirect('admin/deal_with_range_image/list');
        }
    }

    public function remove(Request $request, $id = 0){
        $division = DealWithRangeimage::findOrFail($id);
		if (!empty($division)) {
			$division->delete();
			$request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
		}
		return redirect('admin/deal_with_range_image/list');
    } 
}
