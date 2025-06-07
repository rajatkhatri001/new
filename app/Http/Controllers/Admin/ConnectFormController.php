<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConnectForm;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\View;

class ConnectFormController extends Controller
{
    //
    public function list(Request $request)
    {

        if ($request->ajax()) {

            $data = ConnectForm::where('module','franchise')->get();
            return DataTables::of($data)
            ->addColumn('action', function ($row) {
                $btn['id'] = $row->id;
                $btn['delete'] = $row->id;
                return View::make('Admin.ConnectForm.action', compact('btn'))->render();
            })
            ->rawColumns(['action'])
                ->make(true);
        }
        return view('Admin.ConnectForm.list');
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string',
            'phone' => 'required|string|max:10',
        ],
            [
                'name.required' => 'The name is required',
                'name.string' => 'The name is invalid',
                'email.required' => 'The email is required',
                'email.string' => 'The email is invalid',
                'phone.required' => 'The phone is required',
                'phone.string' => 'The phone is invalid',
                'phone.max' => 'The phone must not be greater than 10 characters',
            ]);
        

        $ConnectForm = new ConnectForm();

        $ConnectForm->name = $request->name;
        $ConnectForm->email = $request->email;
        $ConnectForm->phone = $request->phone;
        $ConnectForm->module = $request->module;

        if ($ConnectForm->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_add_msg'));
            return redirect()->back();
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    public function removeConnectForm(Request $request, $id = 0){
        $client = ConnectForm::findOrFail($id);
		if (!empty($client)) {
			$client->delete();
			$request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
		}
		return redirect('admin/connect-form/list');
    }

    public function levelUplist(Request $request)
    {

        if ($request->ajax()) {

            $data = ConnectForm::where('module','levelup')->get();
            return DataTables::of($data)
            ->addColumn('action', function ($row) {
                $btn['id'] = $row->id;
                $btn['delete'] = $row->id;
                return View::make('Admin.ConnectForm.level-up-action', compact('btn'))->render();
            })
            ->rawColumns(['action'])

                ->make(true);
        }
        return view('Admin.ConnectForm.level-up-list');
    }

    public function removeLevelUpForm(Request $request, $id = 0){
        $client = ConnectForm::findOrFail($id);
		if (!empty($client)) {
			$client->delete();
			$request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
		}
		return redirect('admin/level-up-form/list');
    }
}
