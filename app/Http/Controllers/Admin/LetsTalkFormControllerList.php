<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LetstalkForm;
use Illuminate\Support\Facades\View;
use Redirect;
use DataTables;

class LetsTalkFormControllerList extends Controller
{
    public function list(Request $request)
    {

        if ($request->ajax()) {

			$data = LetstalkForm::get();
            return DataTables::of($data)
                ->addColumn('id', function ($row) {
                    return $row->id;
                  })
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('email', function ($row) {
                    return $row->email;
                })
                ->addColumn('phone', function ($row) {
                    return $row->phone;
                })
                ->addColumn('action', function ($row) {
                    $btn['id'] = $row->id;

                    $btn['delete'] = $row->id;
                    return View::make('Admin.LetsTalkForm.action', compact('btn'))->render();
                })
                ->rawColumns(['action', 'id','name','email','phone'])

                ->make(true);
        }
        return view('Admin.LetsTalkForm.list');
    }

    public function remove(Request $request, $id = 0){
        $client = LetstalkForm::findOrFail($id);
		if (!empty($client)) {
			$client->delete();
			$request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
		}
		return redirect('admin/letstalk-form/list');
    }
}
