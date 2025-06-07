<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HomeSlider;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Redirect;
use DataTables;

class HomesliderController extends Controller
{
    public function list(Request $request)
    {

        if ($request->ajax()) {

			$data = HomeSlider::get();
            return DataTables::of($data)
            ->addColumn('image', function ($row) {
                if ($row->image != "") {
                    $certificateImg = asset('storage/app/public/uploads/HomeSliders') . '/' . $row->image;
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
                ->addColumn('title', function ($row) {
                    return strlen($row->title) > 25 ? substr(strip_tags($row->title), 0, 25) . '..' : strip_tags($row->title);
                  })
                ->addColumn('button_title', function ($row) {
                    return $row->button_title;
                })
                ->addColumn('action', function ($row) {
                    $btn['id'] = $row->id;
                    $btn['edit'] = $row->id;
                    $btn['delete'] = $row->id;
                    return View::make('Admin.HomeSlider.action', compact('btn'))->render();
                })
                ->rawColumns(['action', 'status','button_title','image'])

                ->make(true);
        }
        return view('Admin.HomeSlider.list');
    }

    public function add()
	{
		return view('Admin.HomeSlider.add', ['blogObjs' => []]);
	}

    public function edit(Request $request, $id)
	{
		$blogObjs = HomeSlider::where('id', $id)->first();
		return view('Admin.HomeSlider.add',compact('blogObjs'));
	}

    public function store(Request $request){
        $input = $request->all();

        $rules = [
            // 'title' => 'required|string',
            // 'description' => 'required|string',
            // 'button_title' => 'required|string',
            // 'button_link' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];

        $message = [
            'image.required' => 'The Image is required',
            'image.mimes'=>'Please upload only image files of type: PNG, JPG, JPEG, WEBP',
            'image.max' => 'The file size must be less than 2 MB',
            // 'title.required' => trans('validation.title_required'),
            // 'description.required' => 'The Description is required',
            // 'button_title.required' => 'The Button Title is required',
            // 'button_link.required' => 'The Button Link is required',
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
			$path = $img->storeAs('uploads/HomeSliders', $sectorImgUpload, 'public');
		}

        $blog = new HomeSlider;

        $blog->image = $sectorImgUpload;
        $blog->title = $request->title;
        $blog->description = $request->description;
        $blog->button_title = $request->button_title;
        $blog->button_link = $request->button_link;
        $blog->status = $request->status ? 1 : 0;
        $blog->save();

        $request->session()->flash('alert-success', trans('admin_messages.record_add_msg'));
		return redirect('admin/homeSlider/list');
    }

    public function update(Request $request){
        $blogObjs = HomeSlider::where('id', $request->id)->first();


        if ($request->hasFile('image')){
            $oldImagePath = 'public/HomeSliders/' . $blogObjs->image;
				if (File::exists($oldImagePath)) {
					File::delete($oldImagePath);
				}
                $mainImage = $request->file('image');
                $mainImageUpload = time() . '.' . $mainImage->getClientOriginalExtension();
                $path2 = $mainImage->storeAs('uploads/HomeSliders', $mainImageUpload, 'public');
                $blogObjs->image = $mainImageUpload;

        }
		$blogObjs->title = $request->title;
        $blogObjs->description = $request->description;
        $blogObjs->button_title = $request->button_title;
        $blogObjs->button_link = $request->button_link;
        $blogObjs->status = $request->status ? 1 : 0;
		$blogObjs->save();
		$request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
		return redirect('admin/homeSlider/list');
    }

    public function statusChange(Request $request, $id = 0)
    {
        try {
            $blog = HomeSlider::where('id', $id)->first();
            $blog->status = ($blog->status == 0 ? 1 : 0);
            $blog->save();
        } catch (Exception $ex) {
            $request->session()->flash('alert-danger', trans('admin_messages.something_went'));
            return redirect('admin/homeSlider/list');
        }
    }

    public function remove(Request $request, $id = 0){
        $blog = HomeSlider::findOrFail($id);
		if (!empty($blog)) {

            $mainImageFileName = $blog->image;
			if (!empty($mainImageFileName)) {

                $mainImagePath = 'public/uploads/HomeSliders/' . $mainImageFileName;
				if (File::exists($mainImagePath)) {
                    File::delete($mainImagePath);
				}
			}
			$blog->delete();
			$request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
		}
		return redirect('admin/homeSlider/list');
    }


}
