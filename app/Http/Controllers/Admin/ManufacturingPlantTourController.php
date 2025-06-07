<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ManufacturingplantTour;
use App\Models\ManufacturingpantTourImage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Redirect;
use DataTables;

class ManufacturingPlantTourController extends Controller
{
    public function edit()
    {
        $companyProfile = ManufacturingplantTour::firstOrNew();
        return view('Admin.ManufacturingplantTour.Banner.edit', compact('companyProfile'));
    }

    public function update(Request $request)
    {

        $request->validate([
            'description' => 'required|string',
            'image' => 'required_if:old_image,==,null|image|mimes:jpeg,png,jpg,webp|max:2048',
            'title' => 'required|string',
            'address' => 'required|string',
            'website' => 'required|string',
            'email' => 'required|email',
            'address_iframe' => 'required|string',
            'phone_no' => 'required|digits:10',
        ],
        [
            'description.required' => 'Please enter a description.',
            'image.required' => 'Please upload an image.',
            'image.required_if' => 'Please upload an image.',
            'image.max' => 'The file size must be less than 2 MB',
            'title.required' => 'Please enter a title.',
            'address.required' => 'Please enter an address.',
            'website.required' => 'Please enter a website.',
            'email.required' => 'Please enter an email address.',
            'email.email' => 'Please enter a valid email address.',
            'address_iframe.required' => 'Please enter an address iframe.',
            'phone_no.required' => 'Please enter a phone number.',
            'phone_no.digits' => 'Phone number must be exactly 10 digits long.',
        ]);

        // $img = $request->image;

        // if ($img) {
		// 	$sectorImgUpload = time() . '.' . $img->getClientOriginalExtension();
		// 	$path = $img->storeAs('uploads/ManufacturingPlantTour', $sectorImgUpload, 'public');
		// }

        $companyProfile = ManufacturingplantTour::firstOrNew();

        if ($request->hasFile('image')) {
            if ($companyProfile->image) {
                Storage::disk('public')->delete('uploads/ManufacturingPlantTour/' . $companyProfile->image);
            }
            $image = $request->image;
            $imageUpload = time() . '.' . $request->image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/ManufacturingPlantTour', $imageUpload, 'public');
            $companyProfile->image = $imageUpload;
        }


        $companyProfile->title = $request->title;
        $companyProfile->description = $request->description;
        // $companyProfile->image = $sectorImgUpload;
        $companyProfile->address = $request->address;
        $companyProfile->address_iframe = $request->address_iframe;
        $companyProfile->website = $request->website;
        $companyProfile->email = $request->email;
        $companyProfile->phone_no = $request->phone_no;


        if ($companyProfile->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect()->route('admin.manufacturingplant_tour_off.edit');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    public function list(Request $request)
    {

        if ($request->ajax()) {

			$data = ManufacturingpantTourImage::get();
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
                    $thumbUrl = asset(Storage::url('app/public/uploads/ManufacturingPlantTour/image/' . $row->image));
					return $thumbUrl;
				})
                ->addColumn('category', function ($row) {
                    return $row->category;
                })
                ->addColumn('action', function ($row) {
                    $btn['id'] = $row->id;
                    $btn['edit'] = $row->id;
                    $btn['delete'] = $row->id;
                    return View::make('Admin.ManufacturingplantTour.Images.action', compact('btn'))->render();
                })
                ->rawColumns(['action', 'status', 'image','category'])
                ->make(true);
        }
        return view('Admin.ManufacturingplantTour.Images.list');
    }

    public function add()
	{
		return view('Admin.ManufacturingplantTour.Images.add');
	}

    public function edit_image(Request $request, $id)
	{
		$corporateOfficeTourImage = ManufacturingpantTourImage::where('id', $id)->first();
		return view('Admin.ManufacturingplantTour.Images.add',compact('corporateOfficeTourImage'));
	}

    public function store_image(Request $request){

        $input = $request->all();

        $rules = [

            'image' => 'required',
            'category' => 'required|string',
        ];

        $message = [
            'image.mimes'=>'Please upload only image files of type: PNG, JPG, JPEG, WEBP',
            'image.required' => 'Image field is required',
            'category.required' => 'Category field is required',
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
			$path = $img->storeAs('uploads/ManufacturingPlantTour/image', $sectorImgUpload, 'public');
		}

        $mpimage = new ManufacturingpantTourImage;

        $mpimage->image = $sectorImgUpload;
        $mpimage->category = $request->category;
        $mpimage->status = $request->status ? 1 : 0;
        $mpimage->save();

        $request->session()->flash('alert-success', trans('admin_messages.record_add_msg'));
		return redirect('admin/manufacturingplant_tour/list');
    }

    public function update_image(Request $request){
        $mpimage = ManufacturingpantTourImage::where('id', $request->id)->first();


        if ($request->hasFile('image')){
            $oldImagePath = 'public/ManufacturingPlantTour/image/' . $mpimage->image;
				if (File::exists($oldImagePath)) {
					File::delete($oldImagePath);
				}
                $mainImage = $request->file('image');
                $mainImageUpload = time() . '.' . $mainImage->getClientOriginalExtension();
                $path2 = $mainImage->storeAs('uploads/ManufacturingPlantTour/image', $mainImageUpload, 'public');
                $mpimage->image = $mainImageUpload;

        }
        $mpimage->status = $request->status ? 1 : 0;
        $mpimage->category = $request->category;
		$mpimage->save();
		$request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
		return redirect('admin/manufacturingplant_tour/list');
    }

    public function statusChange(Request $request, $id = 0)
    {
        try {
            $mpimage = ManufacturingpantTourImage::where('id', $id)->first();
            $mpimage->status = ($mpimage->status == 0 ? 1 : 0);
            $mpimage->save();
        } catch (Exception $ex) {
            $request->session()->flash('alert-danger', trans('admin_messages.something_went'));
            return redirect('admin/manufacturingplant_tour/list');
        }
    }

    public function remove(Request $request, $id = 0){
        $mpimage = ManufacturingpantTourImage::findOrFail($id);
		if (!empty($mpimage)) {

            $mainImageFileName = $mpimage->image;
			if (!empty($mainImageFileName)) {

                $mainImagePath = 'public/uploads/ManufacturingPlantTour/image/' . $mainImageFileName;
				if (File::exists($mainImagePath)) {
                    File::delete($mainImagePath);
				}
			}
			$mpimage->delete();
			$request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
		}
		return redirect('admin/manufacturingplant_tour/list');
    }
}
