<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DowloadBrochure;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Redirect;
use DataTables;

class DowloadBrochureController extends Controller
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
                ->editColumn('pdf', function ($row) {
                    $thumbUrl = asset(Storage::url('app/public/uploads/DowloadBrochure/' . $row->image));
					return $thumbUrl;
				})
                ->addColumn('category', function ($row) {
                    return $row->name;
                })
                ->addColumn('action', function ($row) {
                    $btn['id'] = $row->id;
                    $btn['edit'] = $row->id;
                    $btn['delete'] = $row->id;
                    return View::make('Admin.DowloadBrochure.action', compact('btn'))->render();
                })
                ->rawColumns(['action', 'status', 'pdf', 'category'])
                ->make(true);
        }
        return view('Admin.DowloadBrochure.list');
    }

    public function add()
	{
		return view('Admin.DowloadBrochure.add');
	}

    public function edit(Request $request, $id)
	{
		$companyProfile = DowloadBrochure::where('id', $id)->first();
		return view('Admin.DowloadBrochure.add',compact('companyProfile'));
	}

    public function store(Request $request){

        // print_r($request->all()); exit;

        $input = $request->all();

        $rules = [
            'category' => 'required|string',
            'pdf' => 'required',
        ];

        $message = [
            'pdf.required' => 'Please upload an pdf file.',
            'pdf.pdf' => 'The file must be an pdf.',
            'category.required' => 'The Category is required',
            'category.string' => 'The Category is in valid',
            
        ];

        $validator = Validator::make($input, $rules, $message);
		if ($validator->fails()) {
			return Redirect::back()
				->withErrors($validator)
				->withInput();
		}

        $img = $request->pdf;
        if ($img) {
			$sectorImgUpload = time() . '.' . $img->getClientOriginalExtension();
			$path = $img->storeAs('uploads/DowloadBrochure', $sectorImgUpload, 'public');
		}



        $manufac = new DowloadBrochure;

        $manufac->pdf = $sectorImgUpload;
        $manufac->category = $request->category;
        $manufac->status = $request->status ? 1 : 0;
        $manufac->save();

        $request->session()->flash('alert-success', trans('admin_messages.record_add_msg'));
		return redirect('admin/dowload_brochure/list');
    }

    public function update(Request $request){
        $manufac = DowloadBrochure::where('id', $request->id)->first();


        if ($request->hasFile('pdf')){
            $oldImagePath = 'public/DowloadBrochure/' . $manufac->pdf;
				if (File::exists($oldImagePath)) {
					File::delete($oldImagePath);
				}
                $mainImage = $request->file('pdf');
                $mainImageUpload = time() . '.' . $mainImage->getClientOriginalExtension();
                $path2 = $mainImage->storeAs('uploads/DowloadBrochure', $mainImageUpload, 'public');
                $manufac->pdf = $mainImageUpload;

        }
        $manufac->pdf = $sectorImgUpload;
        $manufac->category = $request->category;
        $manufac->status = $request->status ? 1 : 0;
		$manufac->save();
		$request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
		return redirect('admin/dowload_brochure/list');
    }

    public function statusChange(Request $request, $id = 0)
    {
        try {
            $manufac = TrustedManufacturer::where('id', $id)->first();
            $manufac->status = ($manufac->status == 0 ? 1 : 0);
            $manufac->save();
        } catch (Exception $ex) {
            $request->session()->flash('alert-danger', trans('admin_messages.something_went'));
            return redirect('admin/dowload_brochure/list');
        }
    }

    public function remove(Request $request, $id = 0){
        $manufac = TrustedManufacturer::findOrFail($id);
		if (!empty($manufac)) {

            $mainImageFileName = $manufac->pdf;
			if (!empty($mainImageFileName)) {

                $mainImagePath = 'public/uploads/DowloadBrochure/' . $mainImageFileName;
				if (File::exists($mainImagePath)) {
                    File::delete($mainImagePath);
				}
			}
			$manufac->delete();
			$request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
		}
		return redirect('admin/dowload_brochure/list');
    } 
}
