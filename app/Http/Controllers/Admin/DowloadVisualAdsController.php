<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DowloadVisualAdsCategory;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Redirect;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class DowloadVisualAdsController extends Controller
{
    public function list(Request $request)
    {
        if ($request->ajax()) {

            $data = DowloadVisualAdsCategory::get();
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
                ->addColumn('category', function ($row) {
                    return $row->category;
                })
                
                ->addColumn('action', function ($row) {
                    $btn['id'] = $row->id;
                    $btn['edit'] = $row->id;
                    $btn['delete'] = $row->id;
                    return View::make('Admin.DowloadVisualAdsCategory.action', compact('btn'))->render();
                })
                ->rawColumns(['action', 'status', 'image', 'title'])
                ->make(true);
        }
        return view('Admin.DowloadVisualAdsCategory.list');
    }

    public function add()
    {
        return view('Admin.DowloadVisualAdsCategory.add');
    }
    public function store(Request $request)
    {

        $request->validate(
            [
                'pdf' => 'required',
                'category' => 'required|string',

            ],
            [
                'pdf.required' => 'The file is required',
                'category.required' => 'Please enter a Category.',

            ]
        );

        $visualadscategory = new DowloadVisualAdsCategory();
        if ($request->hasFile('pdf')) {
            if ($visualadscategory->pdf) {
                Storage::disk('public')->delete('uploads/visualadsfile/' . $visualadscategory->pdf);
            }
            $image = $request->pdf;
            $path = $image->storeAs('uploads/visualadsfile', $image->getClientOriginalName(), 'public');
            $visualadscategory->pdf = $image->getClientOriginalName();
        }
        $visualadscategory->category = $request->category;
        $visualadscategory->status = $request->status ? 1 : 0;

        if ($visualadscategory->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_add_msg'));
            return redirect()->route('admin.visualadscategory.list');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    public function edit(Request $request, $id)
    {
        $visualadscategory = DowloadVisualAdsCategory::where('id', $id)->first();
        return view('Admin.DowloadVisualAdsCategory.add', compact('visualadscategory'));
    }

    public function update(Request $request)
    {
        $request->validate(
            [
            
                'category' => 'required|string',

            ],
            [
                'pdf.required' => 'The file is required',
                'category.required' => 'Please enter a Category.',

            ]
        );

        $visualadscategory = DowloadVisualAdsCategory::where('id', $request->id)->first();
        if ($request->hasFile('pdf')) {
            if ($visualadscategory->pdf) {
                Storage::disk('public')->delete('uploads/visualadsfile/' . $visualadscategory->pdf);
            }
            $image = $request->pdf;
            // $imageUpload = uniqid() . '.' . $request->pdf->getClientOriginalExtension();
            $path = $image->storeAs('uploads/visualadsfile', $image->getClientOriginalName(), 'public');
            $visualadscategory->pdf = $request->pdf->getClientOriginalExtension();;
        }
        $visualadscategory->category = $request->category;
        $visualadscategory->status = $request->status ? 1 : 0;

        if ($visualadscategory->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect()->route('admin.visualadscategory.list');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    public function statusChange(Request $request, $id = 0)
    {
        try {
            $EventImage = DowloadVisualAdsCategory::where('id', $id)->first();
            $EventImage->status = ($EventImage->status == 0 ? 1 : 0);
            $EventImage->save();
        } catch (Exception $ex) {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    public function remove(Request $request, $id = 0)
    {
        $visualadscategory = DowloadVisualAdsCategory::findOrFail($id);
        if (!empty($visualadscategory)) {
            if ($visualadscategory->pdf) {
                Storage::disk('public')->delete('uploads/visualadsfile/' . $visualadscategory->pdf);
            }
            if ($visualadscategory->delete()) {
                $request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
                return redirect()->route('admin.visualadscategory.list');
            } else {
                $request->session()->flash('alert-error', trans('admin_messages.something_went'));
                return redirect()->back();
            }
        }
        $request->session()->flash('alert-error', trans('admin_messages.something_went'));
        return redirect()->back();
    }
}
