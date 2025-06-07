<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WeBelievePoint;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\Facades\DataTables;

class WeBelievePointController extends Controller
{
    //

    public function list(Request $request)
    {

        if ($request->ajax()) {

            $data = WeBelievePoint::get();
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
                ->addColumn('action', function ($row) {
                    $btn['id'] = $row->id;
                    $btn['edit'] = $row->id;
                    $btn['delete'] = $row->id;
                    return View::make('Admin.WeBelievePoint.action', compact('btn'))->render();
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('Admin.WeBelievePoint.list');
    }

    public function add()
    {
        return view('Admin.WeBelievePoint.add');
    }

    public function edit(Request $request, $id)
    {
        $weBelievePoint = WeBelievePoint::where('id', $id)->first();
        return view('Admin.WeBelievePoint.add', compact('weBelievePoint'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'point' => 'required|string',
        ],
            [
                'point.required' => 'The banner title is required',
                'point.string' => 'The banner title is in valid',
            ]);

        $WeBelievePoint = new WeBelievePoint();
        $WeBelievePoint->point = $request->point;
        $WeBelievePoint->status = $request->status ? 1 : 0;

        if ($WeBelievePoint->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_add_msg'));
            return redirect()->route('admin.we_believe_points.list');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    public function update(Request $request)
    {

        $request->validate([
            'point' => 'required|string',
        ],
            [
                'banner_title.required' => 'The banner title is required',
                'banner_title.string' => 'The banner title is in valid',
            ]);

        $WeBelievePoint = WeBelievePoint::where('id', $request->id)->first();

        $WeBelievePoint->point = $request->point;
        $WeBelievePoint->status = $request->status ? 1 : 0;
        if ($WeBelievePoint->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect()->route('admin.we_believe_points.list');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    public function statusChange(Request $request, $id = 0)
    {
        try {
            $weBelievePoint = WeBelievePoint::where('id', $id)->first();
            $weBelievePoint->status = ($weBelievePoint->status == 0 ? 1 : 0);
            $weBelievePoint->save();
        } catch (Exception $ex) {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    public function remove(Request $request, $id = 0)
    {
        $weBelievePoint = WeBelievePoint::findOrFail($id);
        if (!empty($weBelievePoint)) {
            if ($weBelievePoint->delete()) {
                $request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
                return redirect()->route('admin.we_believe_points.list');
            } else {
                $request->session()->flash('alert-error', trans('admin_messages.something_went'));
                return redirect()->back();
            }
        }
        $request->session()->flash('alert-error', trans('admin_messages.something_went'));
        return redirect()->back();
    }
}
