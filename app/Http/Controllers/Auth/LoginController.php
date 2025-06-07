<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\Admin;
use Auth;
use App;

class LoginController extends Controller
{
    use AuthenticatesUsers;

	public function loginAdmin() {
        // print_r("hello! login."); exit;
		if (\Auth::check()) {
			// return redirect('admin/dashboard');
			return redirect('admin/homeSlider/list');
		} else {
			return view('Auth.login');
		}
	}

	public function loginAdminValidate(Request $request)
	{
        // print_r("hello! login."); exit;
		$this->validate($request, [
			'email' => 'required|email',
			'password' => 'required|min:8',
		], [
			'email.required' => trans('validation.email_required'),
			'email.email' => trans('validation.email_email'),
			'password.min' => trans('validation.password_min'),
			'password.required' => trans('validation.password_required'),
		]);
		$input = $request->all();
		$adminObj = Admin::where("email", $input['email'])->first();
		if (!empty($adminObj)) {
			if (Auth::guard('admin')->attempt(['email' => $input['email'], 'password' => $input['password'], 'status' => 'Active'])) {
                // print_r("hello! login."); exit;
				// return redirect()->route('admin.dashboard');
				return redirect('admin/homeSlider/list');
			} else {
				return redirect()->route('admin.login')->with(['error' => trans('auth.password_incorrect')]);
			}
		} else {
			return redirect()->route('admin.login')->with(['error' => trans('auth.email_not_register')]);
		}
	}

	public function logout(Request $request) {
		Auth::guard('admin')->logout();
		try {
			Auth::guard('admin')->getSession()->flush();
			$request->session()->flush();
		} catch (\Exception $e) {}
		return redirect()->route('admin.login');
	}
}
