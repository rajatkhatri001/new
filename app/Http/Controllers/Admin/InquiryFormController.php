<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SendInquiryForm;
use Illuminate\Support\Facades\View;
use Redirect;
use DataTables;

class InquiryFormController extends Controller
{
    public function list(Request $request)
    {

        if ($request->ajax()) {

			$data = SendInquiryForm::get();
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
                ->addColumn('action', function ($row) {
                    $btn['id'] = $row->id;

                    $btn['delete'] = $row->id;
                    return View::make('Admin.InquiryForm.action', compact('btn'))->render();
                })
                ->rawColumns([ 'action','id','name','email'])

                ->make(true);
        }
        return view('Admin.InquiryForm.list');
    }

    public function remove(Request $request, $id = 0){
        $client = SendInquiryForm::findOrFail($id);
		if (!empty($client)) {
			$client->delete();
			$request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
		}
		return redirect('admin/inquiry-form/list');
    }
}
