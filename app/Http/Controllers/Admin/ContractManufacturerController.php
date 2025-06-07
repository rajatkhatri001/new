<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContractManufacturer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\Facades\DataTables;

class ContractManufacturerController extends Controller
{
    //
    public function list(Request $request)
    {

        if ($request->ajax()) {

            $data = ContractManufacturer::get();
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
                ->addColumn('image', function ($row) {
                    $mainImageUrl = URL::asset('resources/admin-asset/img/default.jpg');
                    if (isset($row->image) && !empty($row->image)) {
                        $mainImageUrl = asset('storage/app/public/uploads/contractmanufacturer/image') . '/' . $row->image;
                    }
                    $html = '<img width="35" htight="35" src="' . $mainImageUrl . '" alt="Image">';
                    return $html;
                })
                ->addColumn('action', function ($row) {
                    $btn['id'] = $row->id;
                    $btn['edit'] = $row->id;
                    $btn['delete'] = $row->id;
                    return View::make('Admin.ContractManufacturer.action', compact('btn'))->render();
                })
                ->rawColumns(['action', 'status', 'image'])
                ->make(true);
        }
        return view('Admin.ContractManufacturer.list');
    }

    public function add()
    {
        return view('Admin.ContractManufacturer.add');
    }

    public function edit(Request $request, $id)
    {
        $contractManufacturer = ContractManufacturer::where('id', $id)->first();
        return view('Admin.ContractManufacturer.add', compact('contractManufacturer'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'image' => 'required_if:old_image,==,null|image|mimes:jpeg,png,jpg,webp|max:2048',
        ],
            [
                'image.mimes' => 'Please upload only image files of type: PNG, JPG, JPEG, WEBP',
                'image.image' => 'The image is in valid',
                'image.required_if' => 'The image is required',
                'image.max' => 'The file size must be less than 2 MB',
            ]);

        $contractManufacturer = new ContractManufacturer();
        if ($request->hasFile('image')) {
            if ($contractManufacturer->image) {
                Storage::disk('public')->delete('uploads/contractmanufacturer/image/' . $contractManufacturer->image);
            }
            $image = $request->image;
            $imageUpload = time() . '.' . $request->image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/contractmanufacturer/image', $imageUpload, 'public');
            $contractManufacturer->image = $imageUpload;
        }
        $contractManufacturer->status = $request->status ? 1 : 0;

        if ($contractManufacturer->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_add_msg'));
            return redirect()->route('admin.contractmanufacturer.list');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'image' => 'required_if:old_image,==,null|image|mimes:jpeg,png,jpg,webp|max:2048',
        ],
            [
                'image.mimes' => 'Please upload only image files of type: PNG, JPG, JPEG, WEBP',
                'image.image' => 'The image is in valid',
                'image.required_if' => 'The image is required',
                'image.max' => 'The file size must be less than 2 MB',
            ]);

        $contractManufacturer = ContractManufacturer::where('id', $request->id)->first();
        if ($request->hasFile('image')) {
            if ($contractManufacturer->image) {
                Storage::disk('public')->delete('uploads/contractmanufacturer/image/' . $contractManufacturer->image);
            }
            $image = $request->image;
            $imageUpload = time() . '.' . $request->image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/contractmanufacturer/image', $imageUpload, 'public');
            $contractManufacturer->image = $imageUpload;
        }
        $contractManufacturer->status = $request->status ? 1 : 0;
        if ($contractManufacturer->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect()->route('admin.contractmanufacturer.list');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    public function statusChange(Request $request, $id = 0)
    {
        try {
            $contractManufacturer = ContractManufacturer::where('id', $id)->first();
            $contractManufacturer->status = ($contractManufacturer->status == 0 ? 1 : 0);
            $contractManufacturer->save();
        } catch (Exception $ex) {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    public function remove(Request $request, $id = 0)
    {
        $contractManufacturer = ContractManufacturer::findOrFail($id);
        if (!empty($contractManufacturer)) {
            if ($contractManufacturer->image) {
                Storage::disk('public')->delete('uploads/contractmanufacturer/image/' . $contractManufacturer->image);
            }
            if ($contractManufacturer->delete()) {
                $request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
                return redirect()->route('admin.contractmanufacturer.list');
            } else {
                $request->session()->flash('alert-error', trans('admin_messages.something_went'));
                return redirect()->back();
            }
        }
        $request->session()->flash('alert-error', trans('admin_messages.something_went'));
        return redirect()->back();
    }
}
