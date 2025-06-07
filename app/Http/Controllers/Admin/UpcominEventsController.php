<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UpcomingEvents;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\Facades\DataTables;

class UpcominEventsController extends Controller
{
    public function list(Request $request)
    {

        if ($request->ajax()) {

            $data = UpcomingEvents::get();
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
                        $mainImageUrl = asset('storage/app/public/uploads/upcomingevents/image') . '/' . $row->image;
                    }
                    $html = '<img width="35" htight="35" src="' . $mainImageUrl . '" alt="Image">';
                    return $html;
                })
                ->addColumn('title', function ($row) {
                    return strlen($row->title) > 25 ? substr(strip_tags($row->title), 0, 25) . '..' : strip_tags($row->title);
                })
                ->addColumn('action', function ($row) {
                    $btn['id'] = $row->id;
                    $btn['edit'] = $row->id;
                    $btn['delete'] = $row->id;
                    return View::make('Admin.UpcomiEvent.action', compact('btn'))->render();
                })
                ->rawColumns(['action', 'status', 'image','title'])
                ->make(true);
        }
        return view('Admin.UpcomiEvent.list');
    }

    public function add()
    {
        return view('Admin.UpcomiEvent.add');
    }

    public function edit(Request $request, $id)
    {
        $Event = UpcomingEvents::where('id', $id)->first();
        return view('Admin.UpcomiEvent.add', compact('Event'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'image' => 'required_if:old_image,==,null|image|mimes:jpeg,png,jpg,webp|max:2048',
            'title' => 'required|string',
            'start_date' => 'nullable',

        ],
            [
                'image.mimes' => 'Please upload only image files of type: PNG, JPG, JPEG, WEBP',
                'image.image' => 'The image is in valid',
                'image.max' => 'The file size must be less than 2 MB',
                'image.required_if' => 'The image is required',
                'title.required' => 'The title is required',
                'title.string' => 'The title is in valid',


            ]);

        $Event = new UpcomingEvents();
        if ($request->hasFile('image')) {
            if ($Event->image) {
                Storage::disk('public')->delete('uploads/upcomigevents/image/' . $Event->image);
            }
            $image = $request->image;
            $imageUpload = time() . '.' . $request->image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/upcomingevents/image', $imageUpload, 'public');
            $Event->image = $imageUpload;
        }
        $Event->title = $request->title;
        $Event->start_date = $request->start_date;
        $Event->status = $request->status ? 1 : 0;

        if ($Event->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_add_msg'));
            return redirect()->route('admin.upcoming-events.list');
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

        ],
            [
                'image.mimes' => 'Please upload only image files of type: PNG, JPG, JPEG, WEBP',
                'image.image' => 'The image is in valid',
                'image.max' => 'The file size must be less than 2 MB',
                'image.required_if' => 'The image is required',
                'title.required' => 'The title is required',
                'title.string' => 'The title is in valid',


            ]);

        $Event = UpcomingEvents::where('id', $request->id)->first();
        if ($request->hasFile('image')) {
            if ($Event->image) {
                Storage::disk('public')->delete('uploads/upcomingevents/image/' . $Event->image);
            }
            $image = $request->image;
            $imageUpload = time() . '.' . $request->image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/upcomingevents/image', $imageUpload, 'public');
            $Event->image = $imageUpload;
        }
        $Event->title = $request->title;
        $Event->start_date = $request->start_date;
        $Event->status = $request->status ? 1 : 0;

        if ($Event->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect()->route('admin.upcoming-events.list');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    public function statusChange(Request $request, $id = 0)
    {
        try {
            $Event = UpcomingEvents::where('id', $id)->first();
            $Event->status = ($Event->status == 0 ? 1 : 0);
            $Event->save();
        } catch (Exception $ex) {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    public function remove(Request $request, $id = 0)
    {
        $Event = UpcomingEvents::findOrFail($id);
        if (!empty($Event)) {
            if ($Event->image) {
                Storage::disk('public')->delete('uploads/upcomingevents/image/' . $Event->image);
            }
            if ($Event->delete()) {
                $request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
                return redirect()->route('admin.upcoming-events.list');
            } else {
                $request->session()->flash('alert-error', trans('admin_messages.something_went'));
                return redirect()->back();
            }
        }
        $request->session()->flash('alert-error', trans('admin_messages.something_went'));
        return redirect()->back();
    }
}
