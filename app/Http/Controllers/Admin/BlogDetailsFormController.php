<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogDetailsForm;
use Illuminate\Support\Facades\Validator;

class BlogDetailsFormController extends Controller
{
    public function index(Request $request)
    {
        $input = $request->all();
        $rules = [
            'name' => 'required|string',
            'comment' => 'required|string',
            'blog_id' => 'required|numeric', // Assuming blog_id should be validated
        ];
    
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first() // Send back the first error found
            ], 400); // Return a 400 Bad Request status code
        }
    
        $ContactUsForm = new BlogDetailsForm();
        $ContactUsForm->name = $request->name;
        $ContactUsForm->blog_id = $request->blog_id;
        $ContactUsForm->comment = $request->comment;
    
        if ($ContactUsForm->save()) {
            return response()->json(['message' => 'Form submitted successfully!']);
        } else {
            return response()->json(['message' => 'Form not submitted!'], 500);
        }
    }
    
}
