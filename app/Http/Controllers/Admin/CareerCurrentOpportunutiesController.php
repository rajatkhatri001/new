<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CareerCurrentOpportunitiesForm;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Redirect;
use DataTables;
use URL;

class CareerCurrentOpportunutiesController extends Controller
{
    public function list(Request $request){

        if ($request->ajax()) {

			$data = CareerCurrentOpportunitiesForm::get();
            return DataTables::of($data)
            ->addColumn('id', function ($row) {
                return $row->opportunities_id;
            })
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('email', function ($row) {
                    return $row->email;
                })
                ->addColumn('subject', function ($row) {
                    return $row->subject;
                })
                ->addColumn('action', function ($row) {
                    $btn['id'] = $row->id;
                    $btn['delete'] = $row->id;
                    $btn['view'] = $row->id;
                    return View::make('Admin.CareerForm.action', compact('btn'))->render();
                })
                // ->rawColumns(['action', 'status', 'thumbnail'])
                ->rawColumns(['action','name','email','subject'])

                ->make(true);
        }
        return view('Admin.CareerForm.list');

    }

    public function add(Request $request){
        // print_r($request->resume);exit;
        $input = $request->all();

        $img = $request->resume;
        if ($img) {
			$sectorImgUpload = time() . '.' . $img->getClientOriginalExtension();
			$path = $img->storeAs('uploads/careerformresume', $sectorImgUpload, 'public');
		}

        $client = new CareerCurrentOpportunitiesForm;

        $client->resume =  $sectorImgUpload  ;
        $client->opportunities_id = $request->opportunities_id;
        $client->name = $request->name;
        $client->email = $request->email;
        $client->subject = $request->subject;
        $client->message = $request->message;
        $client->save();
        if ($client->save()) {
            return "Thank You Application Submit Successfully";
        }else {
            return "Sorry Application Submit Not Successfully";
           
        }
    }
    public function remove(Request $request, $id = 0){
        $client = CareerCurrentOpportunitiesForm::findOrFail($id);
		if (!empty($client)) {

            $mainImageFileName = $client->resume;
			if (!empty($mainImageFileName)) {
                $mainImagePath = 'public/uploads/careerformresume/' . $mainImageFileName;
                // print_r($mainImagePath);exit;
				if (File::exists($mainImagePath)) {
                    File::delete($mainImagePath);
				}
			}
			$client->delete();
			$request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
		}
		return redirect('admin/career-form/list');
    }

    public function view(Request $request, $id = 0){
        $client = CareerCurrentOpportunitiesForm::select('resume')->where('id', $id)->first();
        $resume = $client->resume;
        // print_r($resume);
        $path = URL::asset('storage/app/public/uploads/careerformresume') .'/'. $resume;
        $url = url($path);
       return redirect()->to($url);
    }

}
