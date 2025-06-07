<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OurProductsCategory;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use App\Models\OurDivision;
use Redirect;
use DataTables;

class OurproductscategoryController extends Controller
{
    public function list(Request $request)
    {
        // echo 111;exit;
        if ($request->ajax()) {

			$data = OurProductsCategory::get();
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
                ->addColumn('division', function ($row) {
                    return isset($row->divisions) && isset($row->divisions->title) ? strip_tags($row->divisions->title) : '';
                })

                ->addColumn('action', function ($row) {
                    $btn['id'] = $row->id;
                    $btn['edit'] = $row->id;
                    $btn['delete'] = $row->id;
                    return View::make('Admin.OurProductCategory.action', compact('btn'))->render();
                })
                // ->rawColumns(['action', 'status', 'thumbnail'])
                ->rawColumns(['action', 'status','division'])

                ->make(true);
        }
        return view('Admin.OurProductCategory.list');
    }

    public function add()
	{
        $data=OurDivision::where('status', '1')
                           ->get(['title','id']);
		return view('Admin.OurProductCategory.add',compact('data'));
	}

    public function edit(Request $request, $id)
	{
		$companyProfile = OurProductsCategory::where('id', $id)->first();
         $data=OurDivision::where('status', '1')
                           ->get(['title','id']);
		return view('Admin.OurProductCategory.add',compact('companyProfile','data'));
	}

    public function store(Request $request){

        $input = $request->all();

        $rules = [
            'category_name' => 'required|string',
        ];

        $message = [
            'category_name.required' => 'The Product Category is required',
            'category_name.string' => 'The Product Category is in valid',
        ];

        $validator = Validator::make($input, $rules, $message);
		if ($validator->fails()) {
			return Redirect::back()
				->withErrors($validator)
				->withInput();
		}
        $division = new OurProductsCategory;
        $division->category_name = $request->category_name;
        $division->division = $request->division;
        $division->status = $request->status ? 1 : 0;
        $division->save();

        $request->session()->flash('alert-success', trans('admin_messages.record_add_msg'));
		return redirect('admin/our_products_category/list');
    }

    public function update(Request $request){
        $division = OurProductsCategory::where('id', $request->id)->first();

		$division->category_name = $request->category_name;
        $division->division = $request->division;
        $division->status = $request->status ? 1 : 0;
		$division->save();
		$request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
		return redirect('admin/our_products_category/list');
    }

    public function statusChange(Request $request, $id = 0)
    {
        try {
            $division = OurProductsCategory::where('id', $id)->first();
            $division->status = ($division->status == 0 ? 1 : 0);
            $division->save();
        } catch (Exception $ex) {
            $request->session()->flash('alert-danger', trans('admin_messages.something_went'));
            return redirect('admin/our_products_category/list');
        }
    }

    public function remove(Request $request, $id = 0){
        $division = OurProductsCategory::findOrFail($id);
		if (!empty($division)) {
			$division->delete();
			$request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
		}
		return redirect('admin/our_products_category/list');
    }
}
