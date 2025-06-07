<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Director;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Redirect;
use DataTables;

class DirectorController extends Controller
{
    public function list(Request $request)
    {

        if ($request->ajax()) {

			$data = Director::get();
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
                // ->editColumn('thumbnail', function ($row) {
                //     $thumbUrl = asset(Storage::url('app/public/uploads/blog/thumbnail/' . $row->thumbnail));
				// 	return $thumbUrl;
				// })
                ->addColumn('name', function ($row) {
                    // print_r($row->name); exit;
                    return $row->name;
                })
                ->addColumn('designation', function ($row) {
                    return $row->designation;
                })
                ->addColumn('action', function ($row) {
                    $btn['id'] = $row->id;
                    $btn['edit'] = $row->id;
                    $btn['delete'] = $row->id;
                    return View::make('Admin.Director.action', compact('btn'))->render();
                })
                // ->rawColumns(['action', 'status', 'thumbnail'])
                ->rawColumns(['action', 'status','name','designation'])

                ->make(true);
        }
        return view('Admin.Director.list');
    }

    public function add()
	{
		return view('Admin.Director.add', ['blogObjs' => []]);
	}

    public function edit(Request $request, $id)
	{
		$blogObjs = Director::where('id', $id)->first();
		return view('Admin.Director.add',compact('blogObjs'));
	}

    public function store(Request $request){
        $input = $request->all();

        $rules = [
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'description' => 'required|string',
            'fb_link' => 'required|string',
            'insta_link' => 'required|string',
            'twitter_link' => 'required|string',
            'linkedin_link' => 'required|string',
            'youtube_link' => 'required|string',
            'name' => 'required|string',
            'designation' => 'required|string',
        ];

        $message = [
            'image.required' => 'Please upload an image file.',
            'image.image' => 'The file must be an image.',
            'image.max' => 'The file size must be less than 2 MB',
            'image.mimes' => 'Please upload only image files of type: PNG, JPG, JPEG, WEBP.',
            'name.required' => 'The Name is required',
            'designation.required' => 'The Designation is required',
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
			$path = $img->storeAs('uploads/Directors', $sectorImgUpload, 'public');
		}

        $blog = new Director;
        $blog->image = $sectorImgUpload;
        $blog->name = $request->name;
        $blog->designation = $request->designation;
        $blog->description = $request->description;
        $blog->fb_link = $request->fb_link;
        $blog->insta_link = $request->insta_link;
        $blog->twitter_link = $request->twitter_link;
        $blog->youtube_link = $request->youtube_link;
        $blog->linkedin_link = $request->linkedin_link;
        $blog->status = $request->status ? 1 : 0;
        $blog->save();

        $request->session()->flash('alert-success', trans('admin_messages.record_add_msg'));
		return redirect('admin/director/list');
    }

    public function update(Request $request){
        $blogObjs = Director::where('id', $request->id)->first();


        if ($request->hasFile('image')){
            $oldImagePath = 'public/Directors/' . $blogObjs->image;
				if (File::exists($oldImagePath)) {
					File::delete($oldImagePath);
				}
                $mainImage = $request->file('image');
                $mainImageUpload = time() . '.' . $mainImage->getClientOriginalExtension();
                $path2 = $mainImage->storeAs('uploads/Directors', $mainImageUpload, 'public');
                $blogObjs->image = $mainImageUpload;

        }
		$blogObjs->name = $request->name;
        $blogObjs->description = $request->description;
        $blogObjs->fb_link = $request->fb_link;
        $blogObjs->insta_link = $request->insta_link;
        $blogObjs->twitter_link = $request->twitter_link;
        $blogObjs->youtube_link = $request->youtube_link;
        $blogObjs->linkedin_link = $request->linkedin_link;
        $blogObjs->designation = $request->designation;
        $blogObjs->status = $request->status ? 1 : 0;
		$blogObjs->save();
		$request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
		return redirect('admin/director/list');
    }

    public function statusChange(Request $request, $id = 0)
    {
        try {
            $blog = Director::where('id', $id)->first();
            $blog->status = ($blog->status == 0 ? 1 : 0);
            $blog->save();
        } catch (Exception $ex) {
            $request->session()->flash('alert-danger', trans('admin_messages.something_went'));
            return redirect('admin/director/list');
        }
    }

    public function remove(Request $request, $id = 0){
        $blog = Director::findOrFail($id);
		if (!empty($blog)) {

            $mainImageFileName = $blog->image;
			if (!empty($mainImageFileName)) {

                $mainImagePath = 'public/uploads/Directors/' . $mainImageFileName;
				if (File::exists($mainImagePath)) {
                    File::delete($mainImagePath);
				}
			}
			$blog->delete();
			$request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
		}
		return redirect('admin/director/list');
    }


}
