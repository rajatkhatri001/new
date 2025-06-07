<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Carbon\Carbon;

class DashboardController extends Controller {

	public function index() {
		try {
			// $data = [];
			// $totalUserCount = User::count();
			// $totalExhibitorCount = Exhibitor::count();
			// $data['totalUserCount'] = $totalUserCount;
			// $data['totalExhibitorCount'] = $totalExhibitorCount;
			return view('Admin.dashboard');
		} catch (\Exception $ex) {
			if (env('APP_DEBUG') == false && env('APP_ENV') != 'local') {
				Bugsnag::notifyException($ex);
			}
		}
	}
}
