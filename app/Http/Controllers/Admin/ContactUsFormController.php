<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactUsForm;
use Illuminate\Support\Facades\Validator;

class ContactUsFormController extends Controller
{
    function store(Request $request)
    {
        $input = $request->all();

        $rules = [

            'name' => 'required|string',
            'number' => 'required|string',
            'division' => 'required|string',
            'email' => 'required|string',
            'message' => 'required|string',
        ];
        $message = [
                'name.required' => 'The Name is required',
                'name.string' => 'The Name is required',
                'number.required' => 'The Number is required',
                'number.string' => 'The Number is required',
                'division.required' => 'The Division is required',
                'division.string' => 'The Division is required',
                'email.required' => 'The Email is required',
                'email.string' => 'The Email is required',
                'message.required' => 'The Message is required',
                'message.string' => 'The Message is required',
            ];

            $validator = Validator::make($input, $rules, $message);
            if ($validator->fails()) {
                return Redirect::back()
                    ->withErrors($validator)
                    ->withInput();
            }
        $ContactUsForm = new ContactUsForm();
       
        $ContactUsForm->name = $request->name;
        $ContactUsForm->number = $request->number;
        $ContactUsForm->email = $request->email;
        $ContactUsForm->division = $request->division;
        $ContactUsForm->message = $request->message;

        if ($ContactUsForm->save()) {
            return response()->json(['message' => 'Form submitted successfully!']);

        } else {
            return response()->json(['message' => 'Form Not submitted !']);

        }


        
    }
}
