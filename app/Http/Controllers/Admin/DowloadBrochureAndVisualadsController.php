<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DowloadBrochure;
use App\Models\DowloadBrochureCategory;
use App\Models\DowloadVisualAds;
use App\Models\DowloadVisualAdsCategory;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Redirect;
use DataTables;
use URL;
// use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DowloadBrochureAndVisualadsController extends Controller
{
    public function brochurelist(Request $request){

        if ($request->ajax()) {

			$data = DowloadBrochure::get();
            return DataTables::of($data)
            ->addColumn('category', function ($row) {
               $categoryname= DowloadBrochureCategory::where('id', $row->category)->pluck('category')->first();
                return $categoryname;
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
                    return View::make('Admin.DownloadBrochureForm.action', compact('btn'))->render();
                })
                // ->rawColumns(['action', 'status', 'thumbnail'])
                ->rawColumns(['action','name','email','category'])

                ->make(true);
        }
        return view('Admin.DownloadBrochureForm.list');

    }
    public function visualeadslist(Request $request){

        if ($request->ajax()) {

			$data = DowloadVisualAds::get();
            return DataTables::of($data)
            ->addColumn('category', function ($row) {
                $categoryname= DowloadVisualAdsCategory::where('id', $row->category)->pluck('category')->first();
                return $categoryname;
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
                    return View::make('Admin.DownloadVisualAdsForm.action', compact('btn'))->render();
                })
                // ->rawColumns(['action', 'status', 'thumbnail'])
                ->rawColumns(['action','name','email','category'])

                ->make(true);
        }
        return view('Admin.DownloadVisualAdsForm.list');

    }

    public function addbrochure(Request $request)
{
    $input = $request->all();
    $client = new DowloadBrochure;
    $client->name = $request->name;
    $client->email = $request->email;
    $client->category = $request->category;
    $client->save();

    if ($client->save()) {
        $pdf = DowloadBrochureCategory::where('id', $request->category)->pluck('pdf')->first();
        $filePath = 'app/public/uploads/brochurefile/' . $pdf;

        if (Storage::disk('public')->url($filePath)) {
            
            $fileUrl = asset('storage/' . $filePath);
            return response()->json([
                'message' => 'Thank You For Response',
                'file_url' => $fileUrl,
                'pdfname' => $pdf
            ]);
        }else {
            return response()->json([
                'error' => 'Sorry Application Submit Not Successfully',
                
            ]);
        }
    } else {
        Log::error("Client save failed");
        return response()->json(['error' => 'Sorry Application Submit Not Successfully'], 500);
    }
}

 public function addvisualads(Request $request)
{
    $input = $request->all();
    $client = new DowloadVisualAds;
    $client->name = $request->name;
    $client->email = $request->email;
    $client->category = $request->category;
    $client->save();

    if ($client->save()) {
        $pdf = DowloadVisualAdsCategory::where('id', $request->category)->pluck('pdf')->first();
        $filePath = 'app/public/uploads/visualadsfile/' . $pdf;
        if (Storage::disk('public')->url($filePath)) {
            
            $fileUrl = Storage::disk('public')->url($filePath);
            return response()->json([
                'message' => 'Thank You For Response',
                'file_url' => $fileUrl,
                'pdfname' => $pdf
            ]);
        }else {
            return response()->json([
                'error' => 'Sorry Application Submit Not Successfully',
                
            ]);
        }
    } else {
        Log::error("Client save failed");
        return response()->json(['error' => 'Sorry Application Submit Not Successfully'], 500);
    }
}

    public function removebrochure(Request $request, $id = 0){
        $client = DowloadBrochure::findOrFail($id);
		if (!empty($client)) {
			$client->delete();
			$request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
		}
		return redirect('admin/brochure_form/list');
    }
    public function visualeadsremove(Request $request, $id = 0){
        $client = DowloadVisualAds::findOrFail($id);
		if (!empty($client)) {
			$client->delete();
			$request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
		}
		return redirect('admin/visual_ads_form/list');
    }

    
}
