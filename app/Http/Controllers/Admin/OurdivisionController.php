<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OurDivision;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Redirect;
use DataTables;

class OurdivisionController extends Controller
{
    public function list(Request $request)
    {

        if ($request->ajax()) {

			$data = OurDivision::get();
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
                    return View::make('Admin.OurDivision.action', compact('btn'))->render();
                })
                // ->rawColumns(['action', 'status', 'thumbnail'])
                ->rawColumns(['action', 'status','title'])

                ->make(true);
        }
        return view('Admin.OurDivision.list');
    }

    public function add()
	{
		return view('Admin.OurDivision.add');
	}

    public function edit(Request $request, $id)
	{
		$companyProfile = OurDivision::where('id', $id)->first();
		return view('Admin.OurDivision.add',compact('companyProfile'));
	}

    public function store(Request $request){

        $input = $request->all();
        $rules = [
            'title' => 'required|string',
            'description' => 'required|string',
            'image' => 'required',
            // 'download_pdf' => 'required',
        ];

        $message = [
            'image.required' => 'Please upload an image file.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'Please upload only image files of type: PNG, JPG, JPEG, WEBP.',
            'title.required' => 'The Division is required',
            'title.string' => 'The Division is in valid',
            'description.required' => 'The Description is required',
            'description.string' => 'The Description is in valid',
            // 'download_pdf.required' => 'Download pdf field is required',
        ];

        $validator = Validator::make($input, $rules, $message);
		if ($validator->fails()) {
			return Redirect::back()
				->withErrors($validator)
				->withInput();
		}

        // Validate and store image
        if ($request->hasFile('image')) {
            $img = $request->file('image');
            $sectorImgUpload = time() . '.' . $img->getClientOriginalExtension();
            $img->storeAs('public/uploads/Ourdivision', $sectorImgUpload);
        } else {
            $sectorImgUpload = null;
        }

        // Validate and store PDF
        if ($request->hasFile('download_pdf')) {
            $download_pdf = $request->file('download_pdf');
            $dwnldPdf = $download_pdf->getClientOriginalName() . '.' . $download_pdf->getClientOriginalExtension();
            $download_pdf->storeAs('public/uploads/Ourdivision/DownloadPdf', $dwnldPdf);
        } else {
            $dwnldPdf = null;
        }

        $division = new OurDivision;

        $division->image = $sectorImgUpload;
        $division->download_pdf = $dwnldPdf;
        $division->title = $request->title;
        $division->description = $request->description;
        $division->division_link = $request->division_link;
        $division->status = $request->status ? 1 : 0;
        $division->save();

        $request->session()->flash('alert-success', trans('admin_messages.record_add_msg'));
		return redirect('admin/our_division/list');
    }

    public function update(Request $request){

        $input = $request->all();
        $rules = [
            'title' => 'required|string',
            // 'download_pdf' => 'required',
        ];

        $message = [
            'title.required' => 'The Division is required',
            'title.string' => 'The Division is in valid',
            // 'download_pdf.required' => 'Download pdf field is required',
        ];

        $validator = Validator::make($input, $rules, $message);
		if ($validator->fails()) {
			return Redirect::back()
				->withErrors($validator)
				->withInput();
		}
        $division = OurDivision::where('id', $request->id)->first();

        // echo "<pre>";print_r($request->all());die();
        if ($request->hasFile('image')){
            $oldImagePath = 'public/Ourdivision/' . $division->image;
				if (File::exists($oldImagePath)) {
					File::delete($oldImagePath);
				}
                $mainImage = $request->file('image');
                $mainImageUpload = time() . '.' . $mainImage->getClientOriginalExtension();
                $path2 = $mainImage->storeAs('uploads/Ourdivision', $mainImageUpload, 'public');
                $division->image = $mainImageUpload;

        }
        if ($request->hasFile('download_pdf')){
            $oldPdfPath = 'public/Ourdivision/DownloadPdf/' . $division->download_pdf;
				if (File::exists($oldPdfPath)) {
					File::delete($oldPdfPath);
				}
                $mainPdf = $request->file('download_pdf');
                $mainPdfUpload = $mainPdf->getClientOriginalName() . '.' . $mainPdf->getClientOriginalExtension();
                $path2 = $mainPdf->storeAs('uploads/Ourdivision/DownloadPdf', $mainPdfUpload, 'public');
                $division->download_pdf = $mainPdfUpload;

        }
		$division->title = $request->title;
        $division->description = $request->description;
        $division->division_link = empty($request->division_link) ? null : $request->division_link;
        $division->status = $request->status ? 1 : 0;
		$division->save();
		$request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
		return redirect('admin/our_division/list');
    }

    public function statusChange(Request $request, $id = 0)
    {
        try {
            $division = OurDivision::where('id', $id)->first();
            $division->status = ($division->status == 0 ? 1 : 0);
            $division->save();
        } catch (Exception $ex) {
            $request->session()->flash('alert-danger', trans('admin_messages.something_went'));
            return redirect('admin/our_division/list');
        }
    }

    public function remove(Request $request, $id = 0){
        $division = OurDivision::findOrFail($id);
		if (!empty($division)) {

            $mainImageFileName = $division->image;
			if (!empty($mainImageFileName)) {

                $mainImagePath = 'public/uploads/Ourdivision/' . $mainImageFileName;
				if (File::exists($mainImagePath)) {
                    File::delete($mainImagePath);
				}
			}
			$division->delete();
			$request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
		}
		return redirect('admin/our_division/list');
    }
}
