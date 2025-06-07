<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrustedManufacturer;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Redirect;
use DataTables;

class TrustedManufacturersController extends Controller
{
    public function list(Request $request)
    {
        if ($request->ajax()) {

			$data = TrustedManufacturer::get();
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
                    $thumbUrl = asset(Storage::url('app/public/uploads/TrustedManufacturers/' . $row->image));
					return $thumbUrl;
				})
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('action', function ($row) {
                    $btn['id'] = $row->id;
                    $btn['edit'] = $row->id;
                    $btn['delete'] = $row->id;
                    return View::make('Admin.TrustedManufacturers.action', compact('btn'))->render();
                })
                ->rawColumns(['action', 'status', 'image', 'name'])
                ->make(true);
        }
        return view('Admin.TrustedManufacturers.list');
    }

    public function add()
	{
		return view('Admin.TrustedManufacturers.add');
	}

    public function edit(Request $request, $id)
	{
		$companyProfile = TrustedManufacturer::where('id', $id)->first();
		return view('Admin.TrustedManufacturers.add',compact('companyProfile'));
	}

    public function store(Request $request){

        // print_r($request->all()); exit;

        $input = $request->all();

        $rules = [
            'name' => 'required|string',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];

        $message = [
            'image.required' => 'Please upload an image file.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'Please upload only image files of type: PNG, JPG, JPEG, WEBP.',
            'image.max' => 'The file size must be less than 2 MB',
            'name.required' => 'The Title is required',
            'name.string' => 'The Title is in valid',
            'description.required' => 'The Description is required',
            'description.string' => 'The Description is in valid',
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
			$path = $img->storeAs('uploads/TrustedManufacturers', $sectorImgUpload, 'public');
		}



        $manufac = new TrustedManufacturer;

        $manufac->image = $sectorImgUpload;
        $manufac->name = $request->name;
        $manufac->description = $request->description;
        $manufac->status = $request->status ? 1 : 0;
        $manufac->save();

        $request->session()->flash('alert-success', trans('admin_messages.record_add_msg'));
		return redirect('admin/trustedManufacturers/list');
    }

    public function update(Request $request){
        $manufac = TrustedManufacturer::where('id', $request->id)->first();


        if ($request->hasFile('image')){
            $oldImagePath = 'public/TrustedManufacturers/' . $manufac->image;
				if (File::exists($oldImagePath)) {
					File::delete($oldImagePath);
				}
                $mainImage = $request->file('image');
                $mainImageUpload = time() . '.' . $mainImage->getClientOriginalExtension();
                $path2 = $mainImage->storeAs('uploads/TrustedManufacturers', $mainImageUpload, 'public');
                $manufac->image = $mainImageUpload;

        }
		$manufac->name = $request->name;
        $manufac->description = $request->description;
        $manufac->status = $request->status ? 1 : 0;
		$manufac->save();
		$request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
		return redirect('admin/trustedManufacturers/list');
    }

    public function statusChange(Request $request, $id = 0)
    {
        try {
            $manufac = TrustedManufacturer::where('id', $id)->first();
            $manufac->status = ($manufac->status == 0 ? 1 : 0);
            $manufac->save();
        } catch (Exception $ex) {
            $request->session()->flash('alert-danger', trans('admin_messages.something_went'));
            return redirect('admin/trustedManufacturers/list');
        }
    }

    public function remove(Request $request, $id = 0){
        $manufac = TrustedManufacturer::findOrFail($id);
		if (!empty($manufac)) {

            $mainImageFileName = $manufac->image;
			if (!empty($mainImageFileName)) {

                $mainImagePath = 'public/uploads/TrustedManufacturers/' . $mainImageFileName;
				if (File::exists($mainImagePath)) {
                    File::delete($mainImagePath);
				}
			}
			$manufac->delete();
			$request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
		}
		return redirect('admin/trustedManufacturers/list');
    }
}
