<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OurDivisionBanner;
use App\Models\OurDivisionProduct;
use App\Models\OurDivision;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Redirect;
use DataTables;

class OurDivisionPageController extends Controller
{
    public function edit()
    {
        $companyProfile = OurDivisionBanner::firstOrNew();
        return view('Admin.OurDivisionPage.Banner.edit', compact('companyProfile'));
    }

    public function update(Request $request)
    {

        $request->validate([
            // 'description' => 'required|string',
            'image' => 'required_if:old_image,==,null|image|mimes:jpeg,png,jpg,webp|max:2048',
            // 'title' => 'required|string',
        ],
        [
            // 'description.required' => 'Please enter a description.',
            'image.required' => 'Please upload an image.',
            'image.required_if' => 'Please upload an image.',
            'image.max' => 'The file size must be less than 2 MB',
            // 'title.required' => 'Please enter a title.',
        ]);
        $companyProfile = OurDivisionBanner::firstOrNew();

        if ($request->hasFile('image')) {
            if ($companyProfile->image) {
                Storage::disk('public')->delete('uploads/OurDivisionBanner/' . $companyProfile->image);
            }
            $image = $request->image;
            $imageUpload = time() . '.' . $request->image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/OurDivisionBanner', $imageUpload, 'public');
            $companyProfile->image = $imageUpload;
        }

        $companyProfile->title = $request->title;
        $companyProfile->description = $request->description;
        // $companyProfile->image = $sectorImgUpload;

        if ($companyProfile->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect()->route('admin.ourDivision_page_banner.edit');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    public function list_product(Request $request)
    {

        if ($request->ajax()) {

			$data = OurDivisionProduct::get();
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
                ->addColumn('division', function ($row) {
                    return $row->division;
                })

                ->addColumn('action', function ($row) {
                    $btn['id'] = $row->id;
                    $btn['edit'] = $row->id;
                    $btn['delete'] = $row->id;
                    return View::make('Admin.OurDivisionPage.Products.action', compact('btn'))->render();
                })
                ->rawColumns(['action', 'status', 'title','division'])
                ->make(true);
        }
        return view('Admin.OurDivisionPage.Products.list');
    }

    public function add_product()
	{
        $division = OurDivision::all()->map(function($item) {
        $item->title = strip_tags($item->title);
        return $item;
    });
    return view('Admin.OurDivisionPage.Products.add', compact('division'));
    
	}

    public function edit_product(Request $request, $id)
	{
		$corporateOfficeTourImage = OurDivisionProduct::where('id', $id)->first();
        $division = OurDivision::all();
		return view('Admin.OurDivisionPage.Products.add',compact('corporateOfficeTourImage','division'));
	}

    public function store_product(Request $request){

        $input = $request->all();
        $rules = [

            'image' => 'required',
            'category' => 'required|string',
            'title' => 'required|string',
            'division' => 'required|string',
        ];

        $message = [
            'image.mimes'=>'Please upload only image files of type: PNG, JPG, JPEG, WEBP',
            'image.required' => 'Image field is required',
            'category.required' => 'Category field is required',
            'title.required' => 'Title field is required',
            'title.string' => 'Title field is invalid',
            'division.required' => 'Division field is required',
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
			$path = $img->storeAs('uploads/OurDivisionPageProductImage', $sectorImgUpload, 'public');
		}
        $divData = OurDivision::where('title', 'LIKE', '%' . $request->division . '%')
                            ->whereNull('deleted_at')
                            ->first();
        $mpimage = new OurDivisionProduct;

        if($request->category == "1"){
            $catName = 'Neuro';
        }else if($request->category == "2"){
            $catName = 'Cardiac';
        }else if($request->category == "3"){
            $catName = 'General';
        }else{
            $catName = 'Neuro'; 
        }

        $mpimage->image = $sectorImgUpload;
        $mpimage->title = $request->title;
        $mpimage->division = $request->division;
        $mpimage->division_id = isset($divData) ? $divData->id : null;
        $mpimage->category = $catName;
        $mpimage->category_id = $request->category;
        $mpimage->status = $request->status ? 1 : 0;
        $mpimage->save();

        $request->session()->flash('alert-success', trans('admin_messages.record_add_msg'));
		return redirect('admin/ourDivision_page_product/list');
    }

    public function update_product(Request $request){
        $mpimage = OurDivisionProduct::where('id', $request->id)->first();

        $divData = OurDivision::where('title', 'LIKE', '%' . $request->division . '%')
                            ->whereNull('deleted_at')
                            ->first();
        if ($request->hasFile('image')){
            $oldImagePath = 'public/OurDivisionPageProductImage/' . $mpimage->image;
				if (File::exists($oldImagePath)) {
					File::delete($oldImagePath);
				}
                $mainImage = $request->file('image');
                $mainImageUpload = time() . '.' . $mainImage->getClientOriginalExtension();
                $path2 = $mainImage->storeAs('uploads/OurDivisionPageProductImage', $mainImageUpload, 'public');
                $mpimage->image = $mainImageUpload;

        }
        if($request->category == "1"){
            $catName = 'Neuro';
        }else if($request->category == "2"){
            $catName = 'Cardiac';
        }else if($request->category == "3"){
            $catName = 'General';
        }else{
            $catName = 'Neuro';
        }
        $mpimage->status = $request->status ? 1 : 0;
        $mpimage->category = $catName;
        $mpimage->category_id = $request->category;
        $mpimage->title = $request->title;
        $mpimage->division = $request->division;
        $mpimage->division_id = isset($divData) ? $divData->id : null;
		$mpimage->save();
		$request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
		return redirect('admin/ourDivision_page_product/list');
    }

    public function statusChange(Request $request, $id = 0)
    {
        try {
            $mpimage = OurDivisionProduct::where('id', $id)->first();
            $mpimage->status = ($mpimage->status == 0 ? 1 : 0);
            $mpimage->save();
        } catch (Exception $ex) {
            $request->session()->flash('alert-danger', trans('admin_messages.something_went'));
            return redirect('admin/ourDivision_page_product/list');
        }
    }

    public function remove(Request $request, $id = 0){
        $mpimage = OurDivisionProduct::findOrFail($id);
		if (!empty($mpimage)) {

            $mainImageFileName = $mpimage->image;
			if (!empty($mainImageFileName)) {

                $mainImagePath = 'public/uploads/OurDivisionPageProductImage/' . $mainImageFileName;
				if (File::exists($mainImagePath)) {
                    File::delete($mainImagePath);
				}
			}
			$mpimage->delete();
			$request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
		}
		return redirect('admin/ourDivision_page_product/list');
    }

    public function yourControllerMethod()
{
    $division = OurDivision::all()->map(function($item) {
        $item->title = strip_tags($item->title);
        return $item;
    });
    return view('Admin.OurDivisionPage.Products.add', compact('division'));
}

}
