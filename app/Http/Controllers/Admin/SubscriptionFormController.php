<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailSubscription;
use Illuminate\Support\Facades\View;
use Redirect;
use DataTables;

class SubscriptionFormController extends Controller
{
    public function list(Request $request)
    {

        if ($request->ajax()) {

			$data = EmailSubscription::get();
            return DataTables::of($data)
                ->addColumn('id', function ($row) {
                    return $row->id;
                  })
                ->addColumn('email', function ($row) {
                    return $row->email;
                })
                ->addColumn('action', function ($row) {
                    $btn['id'] = $row->id;
                    $btn['delete'] = $row->id;
                    return View::make('Admin.SubscriptionForm.action', compact('btn'))->render();
                })
                ->rawColumns(['id','email', 'action'])

                ->make(true);
        }
        return view('Admin.SubscriptionForm.list');
    }

    public function remove(Request $request, $id = 0){
        $client = EmailSubscription::findOrFail($id);
		if (!empty($client)) {
			$client->delete();
			$request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
		}
		return redirect('admin/subscription-form/list');
    }
}
