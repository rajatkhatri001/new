<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\Facades\DataTables;

class BlogController extends Controller
{
    //
    public function list(Request $request)
    {

        if ($request->ajax()) {

            $data = Blog::get();
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
                        $mainImageUrl = asset('storage/app/public/uploads/blogs/image') . '/' . $row->image;
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
                    return View::make('Admin.Blog.action', compact('btn'))->render();
                })
                ->rawColumns(['action', 'status', 'image'])
                ->make(true);
        }
        return view('Admin.Blog.list');
    }

    public function add()
    {
        return view('Admin.Blog.add');
    }

    public function edit(Request $request, $id)
    {
        $Blog = Blog::where('id', $id)->first();
        return view('Admin.Blog.add', compact('Blog'));
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

        $Blog = new Blog();
        if ($request->hasFile('image')) {
            if ($Blog->image) {
                Storage::disk('public')->delete('uploads/blogs/image/' . $Blog->image);
            }
            $image = $request->image;
            $imageUpload = time() . '.' . $request->image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/blogs/image', $imageUpload, 'public');
            $Blog->image = $imageUpload;
        }
        $Blog->title = $request->title;
        $Blog->description = $request->description;
        $Blog->status = $request->status ? 1 : 0;
        $Blog->is_this_news = $request->is_this_news ? 1 : 0;


        if ($Blog->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_add_msg'));
            return redirect()->route('admin.blogs.list');
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

        $Blog = Blog::where('id', $request->id)->first();
        if ($request->hasFile('image')) {
            if ($Blog->image) {
                Storage::disk('public')->delete('uploads/blogs/image/' . $Blog->image);
            }
            $image = $request->image;
            $imageUpload = time() . '.' . $request->image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/blogs/image', $imageUpload, 'public');
            $Blog->image = $imageUpload;
        }
        $Blog->title = $request->title;
        $Blog->description = $request->description;
        $Blog->status = $request->status ? 1 : 0;
        $Blog->is_this_news = $request->is_this_news ? 1 : 0;


        if ($Blog->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect()->route('admin.blogs.list');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    public function statusChange(Request $request, $id = 0)
    {
        try {
            $Blog = Blog::where('id', $id)->first();
            $Blog->status = ($Blog->status == 0 ? 1 : 0);
            $Blog->save();
        } catch (Exception $ex) {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    public function newsStatusChange(Request $request, $id = 0)
    {
        try {
            $Blog = Blog::where('id', $id)->first();
            $Blog->is_this_news = ($Blog->is_this_news == 0 ? 1 : 0);
            $Blog->save();
        } catch (Exception $ex) {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    public function remove(Request $request, $id = 0)
    {
        $Blog = Blog::findOrFail($id);
        if (!empty($Blog)) {
            if ($Blog->image) {
                Storage::disk('public')->delete('uploads/blogs/image/' . $Blog->image);
            }
            if ($Blog->delete()) {
                $request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
                return redirect()->route('admin.blogs.list');
            } else {
                $request->session()->flash('alert-error', trans('admin_messages.something_went'));
                return redirect()->back();
            }
        }
        $request->session()->flash('alert-error', trans('admin_messages.something_went'));
        return redirect()->back();
    }

    public function uploadImage(Request $request)
	{
		if ($request->hasFile('upload')) {
			$originName = $request->file('upload')->getClientOriginalName();
			$fileName = pathinfo($originName, PATHINFO_FILENAME);
			$extension = $request->file('upload')->getClientOriginalExtension();
			$fileName = $fileName . '_' . time() . '.' . $extension;
			$imageUpload = $request->file('upload')->storeAs('public/uploads/blogs/description', $fileName);
			$url = asset('storage/app/public/uploads/blogs/description/' . $fileName);
			$CKEditorFuncNum = $request->input('CKEditorFuncNum');
			$msg = trans('validation.img_success_msg');
			$response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";
			return $response;
		}
	}
}
