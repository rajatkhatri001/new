<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\Footer;

class FrontFooterController extends Controller
{
    public function index(){


        $description = Footer::first();

        // return view('Front.Layout.footer', compact('description'));
    }
}
