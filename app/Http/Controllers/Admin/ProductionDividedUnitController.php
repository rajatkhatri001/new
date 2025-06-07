<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\ProductionDividedUnit;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use App\Models\OurDivision;
use Redirect;
use DataTables;

class ProductionDividedUnitController extends Controller
{
    public function list(Request $request)
    {
        if ($request->ajax()) {

			$data = ProductionDividedUnit::get();
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
                
                ->addColumn('action', function ($row) {
                    $btn['id'] = $row->id;
                    $btn['edit'] = $row->id;
                    $btn['delete'] = $row->id;
                    return View::make('Admin.ProductionDividedUnit.action', compact('btn'))->render();
                }) 
                ->editColumn('title', function ($row) {
                    return strip_tags($row->title);
                })
                ->editColumn('description', function ($row) {
                    return strip_tags($row->description);
                })
                // ->rawColumns(['action', 'status', 'thumbnail'])
                ->rawColumns(['action', 'status','title','description'])

                ->make(true);
        }
        return view('Admin.ProductionDividedUnit.list');
    }

    public function add()
	{
        $data=ProductionDividedUnit::where('status', '1')
                           ->get(['title']);
		return view('Admin.ProductionDividedUnit.add',compact('data'));
	}

    public function edit(Request $request, $id)
	{ 
		$productiondividedunit = ProductionDividedUnit::where('id', $id)->first();
		return view('Admin.ProductionDividedUnit.add',compact('productiondividedunit'));
	}

    public function store(Request $request){

        $input = $request->all();

        $rules = [
            'image' => 'required_if:old_image,==,null|image|mimes:jpeg,png,jpg,webp|max:2048',
            'title' => 'required|string',
            'description' => 'required|string',
        ];

        $message = [
            'image.required' => 'Please upload an image.',
            'image.required_if' => 'Please upload an image.',
            'image.max' => 'The file size must be less than 2 MB',
            'title.required' => 'The Title is required',
            'title.string' => 'The Title is in valid',
            'description.required' => 'The Description is required',
            'description.string' => 'The Description is in valid',
        ];

        $validator = Validator::make($input, $rules, $message);
		if ($validator->fails()) {
			return Redirect::back()
				->withErrors($validator)
				->withInput();
		}
        
        $division = new ProductionDividedUnit;
        if ($request->hasFile('image')) {
            if ($division->image) {
                Storage::disk('public')->delete('uploads/ProductionDividedUnit/' . $companyProfile->image);
            }
            $image = $request->image;
            $imageUpload = time() . '.' . $request->image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/ProductionDividedUnit', $imageUpload, 'public');
            $division->image = $imageUpload;
        }

        $division->title = $request->title;
        $division->status = $request->status ? 1 : 0;
        $division->description = $request->description;
        $division->save();

        $request->session()->flash('alert-success', trans('admin_messages.record_add_msg'));
		return redirect('admin/production_divided_unit/list');
    }

    public function update(Request $request){
        $division = ProductionDividedUnit::where('id', $request->id)->first();


        if ($request->hasFile('image')){
            $oldImagePath = 'public/ProductionDividedUnit/' . $division->image;
            if (File::exists($oldImagePath)) {
					File::delete($oldImagePath);
				}
                $mainImage = $request->file('image');
                $mainImageUpload = time() . '.' . $mainImage->getClientOriginalExtension();
                $path2 = $mainImage->storeAs('uploads/ProductionDividedUnit', $mainImageUpload, 'public');
                $division->image = $mainImageUpload;

        }
		$division->title = $request->title;
        $division->description = $request->description;
        $division->status = $request->status ? 1 : 0;
		$division->save();
		$request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
		return redirect('admin/production_divided_unit/list');
    }

    public function statusChange(Request $request, $id = 0)
    {
        try {
            $division = ProductionDividedUnit::where('id', $id)->first();
            $division->status = ($division->status == 0 ? 1 : 0);
            $division->save();
        } catch (Exception $ex) {
            $request->session()->flash('alert-danger', trans('admin_messages.something_went'));
            return redirect('admin/production_divided_unit/list');
        }
    }

    public function remove(Request $request, $id = 0){
        $division = ProductionDividedUnit::findOrFail($id);
		if (!empty($division)) {

            $mainImageFileName = $division->image;
			if (!empty($mainImageFileName)) {

                $mainImagePath = 'public/uploads/ProductionDividedUnit/' . $mainImageFileName;
				if (File::exists($mainImagePath)) {
                    File::delete($mainImagePath);
				}
			}
			$division->delete();
			$request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
		}
		return redirect('admin/production_divided_unit/list');
    }
}
