<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Models\EmailLog;
use Redirect;


class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    public function __construct() {
		$this->middleware('guest');
	}

    public function forgotPasswordView() {
		return view('Auth.forgot-password');
	}

    public function forgotPassword(Request $request) {
		try {
			$rules = array(
				'forgot_email_id' => 'required|email',
			);
			$message = [
				'forgot_email_id.required' => trans('validation.email_required'),
				'forgot_email_id.email' => trans('validation.email_email')
			];
			$validator = Validator::make($request->all(), $rules, $message);
			if ($validator->fails()) {
				return Redirect::back()->withErrors($validator)->withInput();
			}
			$email = $request->forgot_email_id;
			$userObj = Admin::where('email', $email)->whereNull('deleted_at')->first();
			if (!empty($userObj)) {
				$userObj->remember_token = md5(rand(111111111, 999999999));
				$userObj->save();
				try {
					EmailLog::generateLog("Admin_Reset_Password", $userObj->id, true, $isAdmin = true); 
				}
				catch (\Exception $e) {
					return redirect()->route('admin.forgot-password')->with(['error' => trans('auth.something_went')]);
				} 
				return redirect()->route('admin.forgot-password')->with(['success' => trans('auth.forgot_password_reset_sent_link')]);
			} else {
				return redirect()->route('admin.forgot-password')->with(['error' => trans('auth.email_not_register')]);
			}
		} catch (\Exception $e) {
			return redirect()->route('admin.forgot-password')->with(['error' => trans('auth.something_went')]);
		}
	}
}