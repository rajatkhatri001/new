<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Validator;
use App\Providers\RouteServiceProvider;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Redirect;
use Auth;
class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    public function adminResetPassword(Request $request, $token)
	{
		if(Auth::guard('admin')->check()) {
        	return redirect()->route('admin.dashboard');
        }
		$adminObj = Admin::Where('remember_token', $token)->first();
		if (!empty($adminObj)) {
			$data['users'] = $adminObj;
			return view('Auth.resets-password', $data);
		} else {
			$msg = trans('auth.reset_password_error');
			$request->session()->flash('alert-danger', $msg);
			return redirect()->route('admin.login');
		}
	}

	public function adminPasswordIsReset(Request $request)
	{
		$rules = [
			'password' => 'required|min:8',
			'c_password' => 'required|same:password',
		];
		$message = [
			'password.required' => trans('validation.password_required'),
			'c_password.required' => trans('validation.c_password_required'),
			'password.min' => trans('validation.password_min'),
			'c_password.same' => trans('validation.c_password_same'),
		];
		$validator = Validator::make($request->all(), $rules, $message);
		if ($validator->fails()) {
			return Redirect::back()->withErrors($validator)->withInput();
		}
		$adminObj = Admin::where('id', '=', $request->id)->first();
		if (!empty($adminObj)) {
			$remember_token = NULL;
			$adminObj->password = bcrypt($request->password);
			$adminObj->remember_token = $remember_token;
			$adminObj->save();
			return redirect()->route('admin.login')->with(['success' => trans('auth.reset_password_success')]);
		} else {
			return redirect()->route('admin.login')->with(['error' => trans('auth.reset_password_error')]);
		}
	}
}
