<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventImage;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\Facades\DataTables;

class EventImageController extends Controller
{
    //
    public function list(Request $request)
    {

        if ($request->ajax()) {

            $data = EventImage::get();
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
                        $mainImageUrl = asset('storage/app/public/uploads/eventimage/image') . '/' . $row->image;
                    }
                    $html = '<img width="35" htight="35" src="' . $mainImageUrl . '" alt="Image">';
                    return $html;
                })
                // ->addColumn('event', function ($row) {

                //     return strlen($row->event->title) > 25 ? substr(strip_tags($row->event->title), 0, 25) . '..' : strip_tags($row->event->title);
                // })
                ->addColumn('action', function ($row) {
                    $btn['id'] = $row->id;
                    $btn['edit'] = $row->id;
                    $btn['delete'] = $row->id;
                    return View::make('Admin.EventImage.action', compact('btn'))->render();
                })
                ->rawColumns(['action', 'status', 'image'])
                ->make(true);
        }
        return view('Admin.EventImage.list');
    }

    public function add()
    {
        $events = Event::where('status','1')->get();
        return view('Admin.EventImage.add',compact('events'));
    }

    public function edit(Request $request, $id)
    {
        $EventImage = EventImage::where('id', $id)->first();
        $events = Event::where('status','1')->get();
        return view('Admin.EventImage.add', compact('EventImage','events'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'image' => 'required_if:old_image,==,null|image|mimes:jpeg,png,jpg,webp|max:2048',
            // 'event_id' => 'required|exists:events,id',

        ],
            [
                'image.mimes' => 'Please upload only image files of type: PNG, JPG, JPEG, WEBP',
                'image.image' => 'The image is in valid',
                'image.required_if' => 'The image is required',
                'image.max' => 'The file size must be less than 2 MB',
                'event_id.required' => 'The event is required',
                'event_id.exists' => 'The event is in valid',
            ]);

        $EventImage = new EventImage();
        if ($request->hasFile('image')) {
            if ($EventImage->image) {
                Storage::disk('public')->delete('uploads/eventimage/image/' . $EventImage->image);
            }
            $image = $request->image;
            $imageUpload = time() . '.' . $request->image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/eventimage/image', $imageUpload, 'public');
            $EventImage->image = $imageUpload;
        }
        // $EventImage->event_id = $request->event_id;
        $EventImage->status = $request->status ? 1 : 0;

        if ($EventImage->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_add_msg'));
            return redirect()->route('admin.eventimage.list');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'image' => 'required_if:old_image,==,null|image|mimes:jpeg,png,jpg,webp|max:2048',
            // 'event_id' => 'required|exists:events,id',

        ],
            [
                'image.mimes' => 'Please upload only image files of type: PNG, JPG, JPEG, WEBP',
                'image.image' => 'The image is in valid',
                'image.required_if' => 'The image is required',
                'image.max' => 'The file size must be less than 2 MB',
                'event_id.required' => 'The event is required',
                'event_id.exists' => 'The event is in valid',
            ]);

        $EventImage = EventImage::where('id', $request->id)->first();
        if ($request->hasFile('image')) {
            if ($EventImage->image) {
                Storage::disk('public')->delete('uploads/eventimage/image/' . $EventImage->image);
            }
            $image = $request->image;
            $imageUpload = time() . '.' . $request->image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/eventimage/image', $imageUpload, 'public');
            $EventImage->image = $imageUpload;
        }
        // $EventImage->event_id = $request->event_id;
        $EventImage->status = $request->status ? 1 : 0;

        if ($EventImage->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect()->route('admin.eventimage.list');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    public function statusChange(Request $request, $id = 0)
    {
        try {
            $EventImage = EventImage::where('id', $id)->first();
            $EventImage->status = ($EventImage->status == 0 ? 1 : 0);
            $EventImage->save();
        } catch (Exception $ex) {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    public function remove(Request $request, $id = 0)
    {
        $EventImage = EventImage::findOrFail($id);
        if (!empty($EventImage)) {
            if ($EventImage->image) {
                Storage::disk('public')->delete('uploads/eventimage/image/' . $EventImage->image);
            }
            if ($EventImage->delete()) {
                $request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
                return redirect()->route('admin.eventimage.list');
            } else {
                $request->session()->flash('alert-error', trans('admin_messages.something_went'));
                return redirect()->back();
            }
        }
        $request->session()->flash('alert-error', trans('admin_messages.something_went'));
        return redirect()->back();
    }
}
