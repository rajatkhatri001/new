<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Models\CurrentOpportunites;
use Redirect;
use DataTables;
use App\Models\NewLaunchBanner;
use App\Models\NewLaunchSlider;
use App\Models\Product;

class NewLaunchController extends Controller
{
    // NewLaunch Banner edit
    public function editNewLaunchBanner()
    {
        $newLaunch = NewLaunchBanner::firstOrNew();
        return view('Admin.NewLaunch.editBanner', compact('newLaunch'));
    }

    //Update NewLaunch banner
    public function updateNewLaunchBanner(Request $request)
    {
        $request->validate([
            'banner_image' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            // 'banner_title' => 'required|string',
            // 'banner_description' => 'required|string',
        ],
        [
            'banner_image.mimes'=>'Please upload only image files of type: PNG, JPG, JPEG, WEBP',
            'banner_image.max' => 'The file size must be less than 2 MB',
            // 'banner_title.required' => 'The banner title is required',
            // 'banner_title.string' => 'The banner title is in valid',
            // 'banner_description.required' => 'The banner description is required',
            // 'banner_description.string' => 'The banner description is in valid',
        ]);
        $newLaunchBanner = NewLaunchBanner::firstOrNew();
        if ($request->hasFile('banner_image')) {
            if ($newLaunchBanner->banner_image) {
                Storage::disk('public')->delete('uploads/newlaunch/' . $newLaunchBanner->banner_image);
            }
            $image = $request->banner_image;
            $imageUpload = uniqid() . '.' . $request->banner_image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/newlaunch', $imageUpload, 'public');
            $newLaunchBanner->banner_image = $imageUpload;
        }

        $newLaunchBanner->banner_title = $request->banner_title;
        $newLaunchBanner->banner_description = $request->banner_description;
        if ($newLaunchBanner->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect()->route('admin.newLaunch.editbanner');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    // Get NewLaunch Slider List Page
    public function newLaunchSliderList(Request $request)
    {
        if ($request->ajax()) {
			$data = NewLaunchSlider::get();
            return DataTables::of($data)
                ->addColumn('image', function ($row) {
                    if ($row->image != "") {
                        $certificateImg = asset('storage/app/public/uploads/newlaunch') . '/' . $row->image;
                    } else {
                        $certificateImg = '';
                    }
                    return $certificateImg;
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        $checkbox = '<div class="custom-control custom-switch switch-primary switch-md ">
                        <input type="checkbox" class="custom-control-input" id="switch-s1-'. $row->id .'" checked="true" onclick="sliderStatusOnOff(\'' . $row->id . '\', this)">
                        <label class="custom-control-label" for="switch-s1-'. $row->id .'"></label>
                    </div>';
                    } else {
                        $checkbox = '<div class="custom-control custom-switch switch-primary switch-md ">
                        <input type="checkbox" class="custom-control-input" id="switch-s1-'. $row->id .'" onclick="sliderStatusOnOff(\'' . $row->id . '\', this)">
                        <label class="custom-control-label" for="switch-s1-'. $row->id .'"></label>
                    </div>';
                    }
                    return $checkbox;
                })
                ->addColumn('action', function ($row) {
                    $btn['id'] = $row->id;
                    $btn['edit'] = $row->id;
                    $btn['delete'] = $row->id;
                    return View::make('Admin.NewLaunch.sliderAction', compact('btn'))->render();
                })
                ->rawColumns(['action', 'status','image'])
                ->make(true);
        }
        return view('Admin.NewLaunch.sliderList');
    }

    // Get Store Slider Image Add Page
    public function addSlider(Request $request, $id = null)
	{
        if ($id) {
            $sliderData = NewLaunchSlider::where('id', $id)->first();
            if ($sliderData) {
                return view('Admin.NewLaunch.addSlider', compact('sliderData'));
            }
        }else {
            return view('Admin.NewLaunch.addSlider');
        }
	}

    // Store New Slider Image
    public function storSlider(Request $request){
        $input = $request->all();
        $rules = [
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'thumb_image' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'title' => 'required|string',
            'description' => 'required|string',
            'button_title' => 'required|string',
        ];

        $message = [
            'image.required' => 'Please upload an image file.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'Please upload only image files of type: PNG, JPG, JPEG, WEBP.',
            'image.max' => 'The file size must be less than 2 MB',
            'thumb_image.required' => 'Please upload an image file.',
            'thumb_image.image' => 'The file must be an image.',
            'thumb_image.mimes' => 'Please upload only image files of type: PNG, JPG, JPEG, WEBP.',
            'thumb_image.max' => 'The file size must be less than 2 MB',
            'title.required' => 'The title is required',
            'description.required' => 'The description is required',
            'button_title.required' => 'The button title is required',
        ];

        $validator = Validator::make($input, $rules, $message);
        if ($validator->fails()) {
            return Redirect::back()
                ->withErrors($validator)
                ->withInput();
        }

        $image = $request->image;
        if ($image) {
            $imageUpload = uniqid() . '.' . $image->getClientOriginalExtension();
            $image_path = $image->storeAs('uploads/newlaunch', $imageUpload, 'public');
        }
        $thumb_image = $request->thumb_image;
        if ($thumb_image) {
            $thumbImageUpload = uniqid() . '.' . $thumb_image->getClientOriginalExtension();
            $image_path = $image->storeAs('uploads/newlaunch', $thumbImageUpload, 'public');
        }
        $data = new NewLaunchSlider;
        $data->image = $imageUpload;
        $data->is_banner = $request->is_banner ? 1 : 0;
        $data->thumb_image = $thumbImageUpload;
        $data->title = $request->title;
        $data->description = $request->description;
        $data->button_title = $request->button_title;
        $data->status = $request->status ? 1 : 0;
        $data->save();

        $request->session()->flash('alert-success', trans('admin_messages.record_add_msg'));
        return redirect('admin/new-launch-slider/slider-list');
    }

    // Update Slider Image
    public function updateSlider(Request $request)
    {
        $input = $request->all();
        $dataObj = NewLaunchSlider::where('id', $request->id)->first();

        if ($request->hasFile('image')){
            $oldrImagePath = 'public/newlaunch/' . $dataObj->image;
            if (File::exists($oldrImagePath)) {
                File::delete($oldrImagePath);
            }
            $Image = $request->file('image');
            $imageUpload = uniqid() . '.' . $Image->getClientOriginalExtension();
            $path = $Image->storeAs('uploads/newlaunch', $imageUpload, 'public');
            $dataObj->image = $imageUpload;
        }
        if ($request->hasFile('thumb_image')){
            $oldrThumbImagePath = 'public/newlaunch/' . $dataObj->thumb_image;
            if (File::exists($oldrThumbImagePath)) {
                File::delete($oldrThumbImagePath);
            }
            $ThumbImage = $request->file('thumb_image');
            $thumbImageUpload = uniqid() . '.' . $ThumbImage->getClientOriginalExtension();
            $thumbImagePath = $ThumbImage->storeAs('uploads/newlaunch', $thumbImageUpload, 'public');
            $dataObj->thumb_image = $thumbImageUpload;
        }
        $dataObj->is_banner = $request->is_banner ? 1 : 0;
        $dataObj->title = $request->title;
        $dataObj->description = $request->description;
        $dataObj->button_title = $request->button_title;
        $dataObj->status = $request->status ? 1 : 0;
		$dataObj->save();
		$request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
		return redirect('admin/new-launch-slider/slider-list');
    }

    // Remove Slider Image
    public function removeSliderImage(Request $request, $id = 0){
        $data = NewLaunchSlider::findOrFail($id);
		if (!empty($data)) {
            $oldImagePath = $data->image;
			if (!empty($oldImagePath)) {

                $objImagePath = 'public/uploads/newlaunch/' . $oldImagePath;
				if (File::exists($objImagePath)) {
                    File::delete($objImagePath);
				}
			}
            $oldThumbImagePath = $data->thumb_image;
			if (!empty($oldThumbImagePath)) {

                $objThumbImagePath = 'public/uploads/newlaunch/' . $oldThumbImagePath;
				if (File::exists($objThumbImagePath)) {
                    File::delete($objThumbImagePath);
				}
			}
			$data->delete();
			$request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
		}
		return redirect('admin/new-launch-slider/slider-list');
    }

     //Slider Image Status change
    public function sliderStatusChange(Request $request, $id = 0)
    {
    try {
        $data = NewLaunchSlider::where('id', $id)->first();
        $data->status = ($data->status == 0 ? 1 : 0);
        $data->save();
    } catch (Exception $ex) {
        $request->session()->flash('alert-danger', trans('admin_messages.something_went'));
        return redirect('admin/new-launch-slider/slider-list');
    }
    }



    // Get Product List Page
    public function newProductList(Request $request)
    {
        if ($request->ajax()) {
			$data = Product::get();
            return DataTables::of($data)
                ->addColumn('title', function ($row) {
                    return $row->title;
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        $checkbox = '<div class="custom-control custom-switch switch-primary switch-md ">
                        <input type="checkbox" class="custom-control-input" id="switch-s1-'. $row->id .'" checked="true" onclick="productStatusOnOff(\'' . $row->id . '\', this)">
                        <label class="custom-control-label" for="switch-s1-'. $row->id .'"></label>
                    </div>';
                    } else {
                        $checkbox = '<div class="custom-control custom-switch switch-primary switch-md ">
                        <input type="checkbox" class="custom-control-input" id="switch-s1-'. $row->id .'" onclick="productStatusOnOff(\'' . $row->id . '\', this)">
                        <label class="custom-control-label" for="switch-s1-'. $row->id .'"></label>
                    </div>';
                    }
                    return $checkbox;
                })
                ->addColumn('action', function ($row) {
                    $btn['id'] = $row->id;
                    $btn['edit'] = $row->id;
                    $btn['delete'] = $row->id;
                    return View::make('Admin.NewLaunch.productAction', compact('btn'))->render();
                })
                ->rawColumns(['action', 'status','title'])
                ->make(true);
        }
        return view('Admin.NewLaunch.newProductList');
    }

    // Get Store New Product Add Page
    public function addProduct(Request $request, $id = null)
	{
        if ($id) {
            $productData = Product::where('id', $id)->first();
            if ($productData) {
                return view('Admin.NewLaunch.addProduct', compact('productData'));
            }
        }else {
            return view('Admin.NewLaunch.addProduct');
        }
	}

    // Store New Product
    public function storProduct(Request $request){
        $input = $request->all();
        $rules = [
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'title' => 'required|string',
            'description' => 'required|string',
        ];

        $message = [
            'image.required' => 'Please upload an image file.',
            'image.mimes' => 'Please upload only image files of type: PNG, JPG, JPEG, WEBP.',
            'image.max' => 'The file size must be less than 2 MB',
            'title.required' => 'The title is required',
            'description.required' => 'The description is required',
            'description.string' => 'The description must be a string.',
        ];

        $validator = Validator::make($input, $rules, $message);
        if ($validator->fails()) {
            return Redirect::back()
                ->withErrors($validator)
                ->withInput();
        }

        $image = $request->image;
        if ($image) {
            $sectorImageUpload = uniqid() . '.' . $image->getClientOriginalExtension();
            $image_path = $image->storeAs('uploads/newlaunch', $sectorImageUpload, 'public');
        }
        $data = new Product;
        $data->image = $sectorImageUpload;
        $data->title = $request->title;
        $data->description = $request->description;
        $data->is_new_product = $request->is_new_product ? 1 : 0;
        $data->status = $request->status ? 1 : 0;
        $data->save();

        $request->session()->flash('alert-success', trans('admin_messages.record_add_msg'));
        return redirect('admin/new-launch-product/product-list');
    }

    // Update Product
    public function updateProduct(Request $request)
    {
        $input = $request->all();
        $dataObj = Product::where('id', $request->id)->first();

        if ($request->hasFile('image')){
            $oldrImagePath = 'public/newlaunch/' . $dataObj->image;
            if (File::exists($oldrImagePath)) {
                File::delete($oldrImagePath);
            }
            $Image = $request->file('image');
            $imageUpload = uniqid() . '.' . $Image->getClientOriginalExtension();
            $path = $Image->storeAs('uploads/newlaunch', $imageUpload, 'public');
            $dataObj->image = $imageUpload;
        }
        $dataObj->title = $request->title;
        $dataObj->description = $request->description;
        $dataObj->is_new_product = $request->is_new_product ? 1 : 0;
        $dataObj->status = $request->status ? 1 : 0;
		$dataObj->save();
		$request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
		return redirect('admin/new-launch-product/product-list');
    }

    // Remove Product
    public function removeProduct(Request $request, $id = 0){
        $data = Product::findOrFail($id);
		if (!empty($data)) {
            $oldImagePath = $data->image;
			if (!empty($oldImagePath)) {

                $objImagePath = 'public/uploads/newlaunch/' . $oldImagePath;
				if (File::exists($objImagePath)) {
                    File::delete($objImagePath);
				}
			}
			$data->delete();
			$request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
		}
		return redirect('admin/new-launch-product/product-list');
    }

     //Product Status change
    public function productStatusChange(Request $request, $id = 0)
    {
        try {
            $data = Product::where('id', $id)->first();
            $data->status = ($data->status == 0 ? 1 : 0);
            $data->save();
        } catch (Exception $ex) {
            $request->session()->flash('alert-danger', trans('admin_messages.something_went'));
            return redirect('admin/new-launch-product/product-list');
        }
    }

}
