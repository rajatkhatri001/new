<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DowloadBrochureCategory;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Redirect;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class DowloadBrochureCategoryController extends Controller
{
    public function list(Request $request)
    {
        if ($request->ajax()) {

            $data = DowloadBrochureCategory::get();
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
                    return View::make('Admin.DowloadBrochureCategory.action', compact('btn'))->render();
                })
                ->rawColumns(['action', 'status', 'image', 'title'])
                ->make(true);
        }
        return view('Admin.DowloadBrochureCategory.list');
    }

    public function add()
    {
        return view('Admin.DowloadBrochureCategory.add');
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

        $brochurecategory = new DowloadBrochureCategory();
        if ($request->hasFile('pdf')) {
            if ($brochurecategory->pdf) {
                Storage::disk('public')->delete('uploads/brochurefile/' . $brochurecategory->pdf);
            }
           $pdf = $request->file('pdf'); // Use 'file' method for file uploads
            $pdfName = $pdf->getClientOriginalName();
            $path = $pdf->storeAs('uploads/brochurefile', $pdfName, 'public');
            $brochurecategory->pdf = $pdfName;
            $brochurecategory->save();
        }

        $brochurecategory->category = $request->category;
        $brochurecategory->status = $request->status ? 1 : 0;

        if ($brochurecategory->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_add_msg'));
            return redirect()->route('admin.brochurecategory.list');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    public function edit(Request $request, $id)
    {
        $brochurecategory = DowloadBrochureCategory::where('id', $id)->first();
        return view('Admin.DowloadBrochureCategory.add', compact('brochurecategory'));
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

        $brochurecategory = DowloadBrochureCategory::where('id', $request->id)->first();
        if ($request->hasFile('pdf')) {
            if ($brochurecategory->pdf) {
                Storage::disk('public')->delete('uploads/brochurefile/' . $brochurecategory->pdf);
            }
           $pdf = $request->file('pdf'); // Use 'file' method for file uploads
            $pdfName = $pdf->getClientOriginalName();
            $path = $pdf->storeAs('uploads/brochurefile', $pdfName, 'public');
            $brochurecategory->pdf = $pdfName;
            $brochurecategory->save();
        }
        $brochurecategory->category = $request->category;
        $brochurecategory->status = $request->status ? 1 : 0;

        if ($brochurecategory->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect()->route('admin.brochurecategory.list');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    public function statusChange(Request $request, $id = 0)
    {
        try {
            $EventImage = DowloadBrochureCategory::where('id', $id)->first();
            $EventImage->status = ($EventImage->status == 0 ? 1 : 0);
            $EventImage->save();
        } catch (Exception $ex) {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    public function remove(Request $request, $id = 0)
    {
        $brochurecategory = DowloadBrochureCategory::findOrFail($id);
        if (!empty($brochurecategory)) {
            if ($brochurecategory->pdf) {
                Storage::disk('public')->delete('uploads/brochurefile/' . $brochurecategory->pdf);
            }
            if ($brochurecategory->delete()) {
                $request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
                return redirect()->route('admin.brochurecategory.list');
            } else {
                $request->session()->flash('alert-error', trans('admin_messages.something_went'));
                return redirect()->back();
            }
        }
        $request->session()->flash('alert-error', trans('admin_messages.something_went'));
        return redirect()->back();
    }
}