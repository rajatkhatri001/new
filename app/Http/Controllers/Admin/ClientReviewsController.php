<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClientReview;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Redirect;
use DataTables;

class ClientReviewsController extends Controller
{
    public function list(Request $request)
    {

        if ($request->ajax()) {

			$data = ClientReview::get();
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
                ->addColumn('image', function ($row) {
                    if ($row->image != "") {
                        $certificateImg = asset('storage/app/public/uploads/ClientReviews') . '/' . $row->image;
                    } else {
                        $certificateImg = '';
                    }
                    return $certificateImg;
                })
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('action', function ($row) {
                    $btn['id'] = $row->id;
                    $btn['edit'] = $row->id;
                    $btn['delete'] = $row->id;
                    return View::make('Admin.ClientReviews.action', compact('btn'))->render();
                })
                // ->rawColumns(['action', 'status', 'thumbnail'])
                ->rawColumns(['action', 'status','name'])

                ->make(true);
        }
        return view('Admin.ClientReviews.list');
    }

    public function add()
	{
		return view('Admin.ClientReviews.add');
	}

    public function edit(Request $request, $id)
	{
		$companyProfile = ClientReview::where('id', $id)->first();
		return view('Admin.ClientReviews.add',compact('companyProfile'));
	}

    public function store(Request $request){

        $input = $request->all();

        $rules = [
            'name' => 'required|string',
            'description' => 'required|string',
            //'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];

        $message = [
            //'image.required'=>'The Image is required',
            //'image.mimes'=>'Please upload only image files of type: PNG, JPG, JPEG, WEBP',
            //'image.max' => 'The file size must be less than 2 MB',
            'name.required' => 'The name is required',
            'name.string' => 'The name is in valid',
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
			$path = $img->storeAs('uploads/ClientReviews', $sectorImgUpload, 'public');
		}

        $client = new ClientReview;

        $client->image = $sectorImgUpload;
        $client->name = $request->name;
        $client->description = $request->description;
        $client->status = $request->status ? 1 : 0;
        $client->save();

        $request->session()->flash('alert-success', trans('admin_messages.record_add_msg'));
		return redirect('admin/client_review/list');
    }

    public function update(Request $request){
        $client = ClientReview::where('id', $request->id)->first();


        // if ($request->hasFile('image')){
        //     $oldImagePath = 'public/ClientReviews/' . $client->image;
		// 		if (File::exists($oldImagePath)) {
		// 			File::delete($oldImagePath);
		// 		}
        //         $mainImage = $request->file('image');
        //         $mainImageUpload = time() . '.' . $mainImage->getClientOriginalExtension();
        //         $path2 = $mainImage->storeAs('uploads/ClientReviews', $mainImageUpload, 'public');
        //         $client->image = $mainImageUpload;

        // }
		$client->name = $request->name;
        $client->description = $request->description;
        $client->status = $request->status ? 1 : 0;
		$client->save();
		$request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
		return redirect('admin/client_review/list');
    }

    public function statusChange(Request $request, $id = 0)
    {
        try {
            $client = ClientReview::where('id', $id)->first();
            $client->status = ($client->status == 0 ? 1 : 0);
            $client->save();
        } catch (Exception $ex) {
            $request->session()->flash('alert-danger', trans('admin_messages.something_went'));
            return redirect('admin/client_review/list');
        }
    }

    public function remove(Request $request, $id = 0){
        $client = ClientReview::findOrFail($id);
		if (!empty($client)) {

            $mainImageFileName = $client->image;
			if (!empty($mainImageFileName)) {

                $mainImagePath = 'public/uploads/ClientReviews/' . $mainImageFileName;
				if (File::exists($mainImagePath)) {
                    File::delete($mainImagePath);
				}
			}
			$client->delete();
			$request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
		}
		return redirect('admin/client_review/list');
    }
}
