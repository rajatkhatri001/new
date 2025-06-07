<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DivisionAndSisterConcern;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\Facades\DataTables;

class DivisionAndSisterConcernController extends Controller
{
    //
    public function list(Request $request)
    {

        if ($request->ajax()) {

            $data = DivisionAndSisterConcern::get();
            return DataTables::of($data)
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        $checkbox = '<div class="custom-control custom-switch switch-primary switch-md ">
                        <input type="checkbox" class="custom-control-input" id="switch-s1-' . $row->id . '" checked="true" onclick="statusOnOff(\'' . $row->id . '\', this)">
                        <label class="custom-control-label" for="switch-s1-' . $row->id . '"></label>
                    </div>';
                    } else {
                        $checkbox = '<div class="custom-control custom-switch switch-primary switch-md ">
                        <input type="checkbox" class="custom-control-input" id="switch-s1-' . $row->id . '" onclick="statusOnOff(\'' . $row->id . '\', this)">
                        <label class="custom-control-label" for="switch-s1-' . $row->id . '"></label>
                    </div>';
                    }
                    return $checkbox;
                })
                ->addColumn('title', function ($row) {
                    return strlen($row->title) > 25 ? substr(strip_tags($row->title), 0, 25) . '..' : strip_tags($row->title);
                  })
                ->addColumn('image', function ($row) {
                    $mainImageUrl = URL::asset('resources/admin-asset/img/default.jpg');
                    if (isset($row->image) && !empty($row->image)) {
                        $mainImageUrl = asset('storage/app/public/uploads/divisionandsisterconcern/image') . '/' . $row->image;
                    }
                    $html = '<img width="35" htight="35" src="' . $mainImageUrl . '" alt="Image">';
                    return $html;
                })
                ->addColumn('action', function ($row) {
                    $btn['id'] = $row->id;
                    $btn['edit'] = $row->id;
                    $btn['delete'] = $row->id;
                    return View::make('Admin.DivisionAndSisterConcern.action', compact('btn'))->render();
                })
                ->rawColumns(['action', 'status', 'image','title'])
                ->make(true);
        }
        return view('Admin.DivisionAndSisterConcern.list');
    }

    public function add()
    {
        return view('Admin.DivisionAndSisterConcern.add');
    }

    public function edit(Request $request, $id)
    {
        $divisionAndSisterConcern = DivisionAndSisterConcern::where('id', $id)->first();
        return view('Admin.DivisionAndSisterConcern.add', compact('divisionAndSisterConcern'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'image' => 'required_if:old_image,==,null|image|mimes:jpeg,png,jpg,webp|max:2048',
            'title' => 'required|string',
            'description' => 'required|string',

        ],
            [
                'image.mimes' => 'Please upload only image files of type: PNG, JPG, JPEG, WEBP',
                'image.image' => 'The image is in valid',
                'image.required_if' => 'The image is required',
                'image.max' => 'The file size must be less than 2 MB',
                'title.required' => 'The title is required',
                'title.string' => 'The title is in valid',
                'description.required' => 'The description is required',
                'description.string' => 'The description is in valid',

            ]);

        $divisionAndSisterConcern = new DivisionAndSisterConcern();
        if ($request->hasFile('image')) {
            if ($divisionAndSisterConcern->image) {
                Storage::disk('public')->delete('uploads/divisionandsisterconcern/image/' . $divisionAndSisterConcern->image);
            }
            $image = $request->image;
            $imageUpload = time() . '.' . $request->image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/divisionandsisterconcern/image', $imageUpload, 'public');
            $divisionAndSisterConcern->image = $imageUpload;
        }
        $divisionAndSisterConcern->title = $request->title;
        $divisionAndSisterConcern->description = $request->description;
        $divisionAndSisterConcern->status = $request->status ? 1 : 0;

        if ($divisionAndSisterConcern->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_add_msg'));
            return redirect()->route('admin.division_and_sister_concern.list');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'image' => 'required_if:old_image,==,null|image|mimes:jpeg,png,jpg,webp|max:2048',
            'title' => 'required|string',
            'description' => 'required|string',

        ],
            [
                'image.mimes' => 'Please upload only image files of type: PNG, JPG, JPEG, WEBP',
                'image.image' => 'The image is in valid',
                'image.required_if' => 'The image is required',
                'image.max' => 'The file size must be less than 2 MB',
                'title.required' => 'The title is required',
                'title.string' => 'The title is in valid',
                'description.required' => 'The description is required',
                'description.string' => 'The description is in valid',

            ]);

        $divisionAndSisterConcern = DivisionAndSisterConcern::where('id', $request->id)->first();
        if ($request->hasFile('image')) {
            if ($divisionAndSisterConcern->image) {
                Storage::disk('public')->delete('uploads/divisionandsisterconcern/image/' . $divisionAndSisterConcern->image);
            }
            $image = $request->image;
            $imageUpload = time() . '.' . $request->image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/divisionandsisterconcern/image', $imageUpload, 'public');
            $divisionAndSisterConcern->image = $imageUpload;
        }
        $divisionAndSisterConcern->title = $request->title;
        $divisionAndSisterConcern->description = $request->description;
        $divisionAndSisterConcern->status = $request->status ? 1 : 0;

        if ($divisionAndSisterConcern->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect()->route('admin.division_and_sister_concern.list');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    public function statusChange(Request $request, $id = 0)
    {
        try {
            $divisionAndSisterConcern = DivisionAndSisterConcern::where('id', $id)->first();
            $divisionAndSisterConcern->status = ($divisionAndSisterConcern->status == 0 ? 1 : 0);
            $divisionAndSisterConcern->save();
        } catch (Exception $ex) {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    public function remove(Request $request, $id = 0)
    {
        $divisionAndSisterConcern = DivisionAndSisterConcern::findOrFail($id);
        if (!empty($divisionAndSisterConcern)) {
            if ($divisionAndSisterConcern->image) {
                Storage::disk('public')->delete('uploads/divisionandsisterconcern/image/' . $divisionAndSisterConcern->image);
            }
            if ($divisionAndSisterConcern->delete()) {
                $request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
                return redirect()->route('admin.division_and_sister_concern.list');
            } else {
                $request->session()->flash('alert-error', trans('admin_messages.something_went'));
                return redirect()->back();
            }
        }
        $request->session()->flash('alert-error', trans('admin_messages.something_went'));
        return redirect()->back();
    }
}
