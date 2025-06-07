<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OurProduct;
use App\Models\OurProductImages;
use App\Models\OurProductsCategory;
use App\Models\ProductImage;
use App\Models\OurDivision;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Redirect;

class OurProductController extends Controller
{
    public function edit()
    {
        $companyProfile = OurProduct::firstOrNew();
        return view('Admin.OurProduct.Banner.edit', compact('companyProfile'));
    }

    public function update(Request $request)
    {

        $request->validate([
            // 'description' => 'required|string',
            'image' => 'required_if:old_image,==,null|image|mimes:jpeg,png,jpg,webp|max:2048',
            // 'title' => 'required|string',
            'product_details_banner' => 'required_if:old_product_details_banner,==,null|image|mimes:jpeg,png,jpg,webp|max:2048',

        ],
            [
                // 'description.required' => 'Please enter a description.',
                'image.required' => 'Please upload an image.',
                'image.required_if' => 'Please upload an image.',
                'image.max' => 'The file size must be less than 2 MB',
                // 'title.required' => 'Please enter a title.',
                'product_details_banner.mimes' => 'Please upload only product details banner image files of type: PNG, JPG, JPEG, WEBP',
                'product_details_banner.image' => 'The product details banner image is in valid',
                'product_details_banner.required_if' => 'The product details banner image is required',
                'product_details_banner.max' => 'The file size must be less than 2 MB',
            ]);

        // $img = $request->image;

        // if ($img) {
        //     $sectorImgUpload = time() . '.' . $img->getClientOriginalExtension();
        //     $path = $img->storeAs('uploads/ManufacturingPlantTour', $sectorImgUpload, 'public');
        // }

        $companyProfile = OurProduct::firstOrNew();

        if ($request->hasFile('image')) {
            if ($companyProfile->image) {
                Storage::disk('public')->delete('uploads/ourproductbanner/' . $companyProfile->image);
            }
            $image = $request->image;
            $imageUpload = time() . '.' . $request->image->getClientOriginalExtension();
            $path = $image->storeAs('uploads/ourproductbanner', $imageUpload, 'public');
            $companyProfile->image = $imageUpload;
        }

        if ($request->hasFile('product_details_banner')) {
            if ($companyProfile->product_details_banner) {
                Storage::disk('public')->delete('uploads/ourproductbanner/product_details_banner/' . $companyProfile->product_details_banner);
            }
            $productDetailsBanner = $request->product_details_banner;
            $productDetailsBannerUpload = time() . '.' . $request->product_details_banner->getClientOriginalExtension();
            $path = $productDetailsBanner->storeAs('uploads/ourproductbanner/product_details_banner', $productDetailsBannerUpload, 'public');
            $companyProfile->product_details_banner = $productDetailsBannerUpload;
        }

        $companyProfile->title = $request->title;
        $companyProfile->description = $request->description;
        // $companyProfile->image = $sectorImgUpload;

        if ($companyProfile->save()) {
            $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
            return redirect()->route('admin.our_product_banner.edit');
        } else {
            $request->session()->flash('alert-error', trans('admin_messages.something_went'));
            return redirect()->back();
        }
    }

    public function list(Request $request)
    {

        if ($request->ajax()) {

            $data = OurProductImages::get();
            return DataTables::of($data)
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        $checkbox = '<div class="custom-control custom-switch switch-primary switch-md ">
                        <input type="checkbox" class="custom-control-input" id="switch-s1-' . $row->id . '" checked="true" onclick="statusOnOff(\'' . $row->id . '\', this)">
                        <label class="custom-control-label" for="switch-s1-' . $row->id . '"></label>
                    </div>';
                    } else {
                        $checkbox = '<div class="custom-control custom-switch switch-primary switch-md ">
                        <input type="checkbox" class="custom-control-input" id="switch-s1-' . $row->id . '" onclick="statusOnOff(\'' . $row->id . '\', this)">
                        <label class="custom-control-label" for="switch-s1-' . $row->id . '"></label>
                    </div>';
                    }
                    return $checkbox;
                })
                ->editColumn('image', function ($row) {
                    $thumbUrl = asset(Storage::url('app/public/uploads/OurProductImage/image/' . $row->image));
                    return $thumbUrl;
                })
                ->addColumn('category', function ($row) {
                    return $row->category;
                })
                ->addColumn('action', function ($row) {
                    $btn['id'] = $row->id;
                    $btn['edit'] = $row->id;
                    $btn['delete'] = $row->id;
                    return View::make('Admin.OurProduct.Products.action', compact('btn'))->render();
                })
                ->rawColumns(['action', 'status', 'image', 'category'])
                ->make(true);
        }
        return view('Admin.OurProduct.Products.list');
    }

    public function add()
    {
        // $category = OurProductsCategory::where('status', '1')
        //     ->get(['category_name', 'id']);
        $division = OurDivision::where('status', '1')
        ->get();
        return view('Admin.OurProduct.Products.add', compact('division'));
    }

    public function edit_product(Request $request, $id)
    {
        $corporateOfficeTourImage = OurProductImages::where('id', $id)->first();
        $division = OurDivision::where('status', '1')
        ->get();
        $category = OurProductsCategory::where('id', $corporateOfficeTourImage->category)->first(['category_name']);
        return view('Admin.OurProduct.Products.add', compact('corporateOfficeTourImage', 'division','category'));
    }

    public function store_product(Request $request)
    {
        $input = $request->all();

        $rules = [

            'image' => 'required',
            'product_title' => 'required|string',
            'product_label' => 'required|string',
            'packing_type' => 'required|string',
            'mrp' => 'required|string',
            'ptr' => 'required|string',
            'pts' => 'required|string',
            'label_2' => 'required|string',
            'category' => 'required|string',
            'division' => 'required|string',
            'composition' => 'required|string',
            'product_description' => 'required|string',
            'product_side_effect' => 'required|string',
            'product_indication' => 'required|string',
        ];

        $message = [
            'image.mimes' => 'Please upload only image files of type: PNG, JPG, JPEG, WEBP',
            'image.required' => 'Image field is required',
            'product_title.required' => 'Product Title field is required',
            'product_label.required' => 'Product Label field is required',
            'packing_type.required' => 'Packing Type field is required',
            'packing_size.required' => 'Packing Size field is required',
            'mrp.required' => 'MRP field is required',
            'ptr.required' => 'PTR field is required',
            'pts.required' => 'PTS field is required',
            'label_2.required' => 'Label 2 field is required',
            'category.required' => 'Category field is required',
            'division.required' => 'division field is required',
            'composition.required' => 'Composition field is required',
            'product_description.required' => 'Product Description field is required',
            'product_side_effect.required' => 'Product Side Effect field is required',
            'product_indication.required' => 'Product indication  field is required',
        ];

        $validator = Validator::make($input, $rules, $message);
        if ($validator->fails()) {
            return Redirect::back()
                ->withErrors($validator)
                ->withInput();
        }

        $img = $request->image;
        if ($img) {
            $sectorImgUpload = time() . '.' . $img->getClientOriginalExtension();
            $path = $img->storeAs('uploads/OurProductImage/image', $sectorImgUpload, 'public');
        }

        $mpimage = new OurProductImages;

        $mpimage->image = $sectorImgUpload;
        $mpimage->product_title = $request->product_title;
        $mpimage->product_label = $request->product_label;
        $mpimage->packing_type = $request->packing_type;
        $mpimage->packing_size = $request->packing_size;
        $mpimage->mrp = $request->mrp;
        $mpimage->ptr = $request->ptr;
        $mpimage->pts = $request->pts;
        $mpimage->category = $request->category;
        $mpimage->division_id = $request->division;
        $mpimage->composition = $request->composition;
        $mpimage->label_2 = $request->label_2;
        $mpimage->product_description = $request->product_description;
        $mpimage->product_side_effect = $request->product_side_effect;
        $mpimage->product_indication = $request->product_indication;
        $mpimage->status = $request->status ? 1 : 0;
        $mpimage->is_new_product = $request->is_new_product ? 1 : 0;
        $mpimage->save();

        $request->session()->flash('alert-success', trans('admin_messages.record_add_msg'));
        return redirect('admin/our_product/list');
    }

    public function update_product(Request $request)
    {
        $mpimage = OurProductImages::where('id', $request->id)->first();

        if ($request->hasFile('image')) {
            $oldImagePath = 'public/OurProductImage/image/' . $mpimage->image;
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }
            $mainImage = $request->file('image');
            $mainImageUpload = time() . '.' . $mainImage->getClientOriginalExtension();
            $path2 = $mainImage->storeAs('uploads/OurProductImage/image', $mainImageUpload, 'public');
            $mpimage->image = $mainImageUpload;

        }
        $mpimage->product_title = $request->product_title;
        $mpimage->product_label = $request->product_label;
        $mpimage->packing_type = $request->packing_type;
        $mpimage->packing_size = $request->packing_size;
        $mpimage->mrp = $request->mrp;
        $mpimage->ptr = $request->ptr;
        $mpimage->pts = $request->pts;
        $mpimage->category = $request->category;
        $mpimage->division_id = $request->division;
        $mpimage->composition = $request->composition;
        $mpimage->label_2 = $request->label_2;
        $mpimage->product_description = $request->product_description;
        $mpimage->product_side_effect = $request->product_side_effect;
        $mpimage->product_indication = $request->product_indication;
        $mpimage->status = $request->status ? 1 : 0;
        $mpimage->is_new_product = $request->is_new_product ? 1 : 0;

        $mpimage->save();
        $request->session()->flash('alert-success', trans('admin_messages.record_update_msg'));
        return redirect('admin/our_product/list');
    }

    public function statusChange(Request $request, $id = 0)
    {
        try {
            $mpimage = OurProductImages::where('id', $id)->first();
            $mpimage->status = ($mpimage->status == 0 ? 1 : 0);
            $mpimage->save();
        } catch (Exception $ex) {
            $request->session()->flash('alert-danger', trans('admin_messages.something_went'));
            return redirect('admin/our_product/list');
        }
    }

    public function remove(Request $request, $id = 0)
    {
        $mpimage = OurProductImages::findOrFail($id);
        if (!empty($mpimage)) {

            $mainImageFileName = $mpimage->image;
            if (!empty($mainImageFileName)) {

                $mainImagePath = 'public/uploads/OurProductImage/image/' . $mainImageFileName;
                if (File::exists($mainImagePath)) {
                    File::delete($mainImagePath);
                }
            }
            $mpimage->delete();
            $request->session()->flash('alert-success', trans('admin_messages.record_delete_msg'));
        }
        return redirect('admin/our_product/list');
    }

    public function uploadImages()
    {
        return view('Admin.OurProduct.Products.uploadImages');
    }

    public function uploadImagesSubmit(Request $request)
    {
        $request->session()->flash('alert-success', 'Product Images Uploaded successfully.');
        return redirect()->route('admin.our_product.list');
    }

    public function uploadProductImage(Request $request)
    {
        $fileUploaded = '';
        $productImage = $request->file('file');
        if ($request->hasFile('file')) {
            $mainImage = $request->file('file');
            $mainImageUpload = $mainImage->getClientOriginalName();
            $path = $mainImage->storeAs('uploads/OurProductImage/product-image', $mainImageUpload, 'public');
            $fileUploaded = $mainImage->getClientOriginalName();
        }
        if ($fileUploaded != '') {
            $productImage = new ProductImage();
            $productImage->image = $fileUploaded;
            $productImage->save();
        }
        return response()->json(['image' => $fileUploaded, 'id' => $productImage->id ?? 0]);
    }

    public function deleteProductImage(Request $request)
    {
        $res = 0;
        if (isset($request->id) && !empty($request->id)) {
            $record = ProductImage::find($request->id);
            if ($record) {
                if ($record->image) {
                    $path = 'public/uploads/OurProductImage/product-image/' . $record->image;
                    if (Storage::exists($path)) {
                        Storage::disk('public')->delete('uploads/OurProductImage/product-image/' . $record->image);
                    }
                }
                $record->delete();
                $res = 1;
            }
        }
        return response()->json(['success' => $res]);
    }

    public function productImport(Request $request)
    {
        $rules = [
            'xlsx_file' => 'required|mimes:xls,xlsx',
        ];
        $message = [
            "xlsx_file.required" => trans('admin_messages.xlsx_file_required'),
            "xlsx_file.mimes" => trans('admin_messages.xlsx_file_mimes_types'),
        ];
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            $msg = $validator->errors()->toArray();
            $request->session()->flash('alert-error', $msg['xlsx_file'][0]);
            return redirect()->back();
        }

        if ($request->hasFile("xlsx_file")) {
            $file = $request->file("xlsx_file");
            $filePath = $file->getRealPath();

            // Read the XLSX file and extract data
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $data = $worksheet->toArray();

            // Remove the header row
            $header = array_shift($data);

            $errors = [];
            $productData = [];
            $data = array_map(function ($value) {
                if (is_array($value)) {
                    return array_map('trim', $value);
                }
                return trim($value);
            }, $data);

            $data = array_filter($data, function ($item1) {
                return array_filter($item1, function ($value) {
                    return !empty($value);
                });
            });
            $seenImages = [];
            $repeatingImages = [];
            foreach ($data as $rowIndex => $row) {

                $rowValidator = Validator::make($row, [
                    "0" => "numeric",
                    "1" => "required", // Category
                    "2" => "required", // Product Lable
                    "3" => "required", // Product Title
                    "4" => "required", // Packing Type
                    "5" => "required", // Packaging Size
                    "6" => "required", // MRP
                    "7" => "required|numeric", // PTR
                    "8" => "nullable|numeric", // PTS
                    "9" => "nullable|numeric", // image
                    "10" => "required", // Composition
                    "11" => "required", // Brand Since
                    "12" => "required", // Description
                    "13" => "required", // Side Effects
                    "14" => "required", // Indication
                    "15" => "required", // Indication
                    "16" => "required", // Division
                ]);

                $customAttributes = [
                    "0" => "Index",
                    "1" => "Category",
                    "2" => "Division",
                    "3" => "Product Lable",
                    "4" => "Product Title",
                    "5" => "Packing Type",
                    "6" => "Packaging Size",
                    "7" => "MRP",
                    "8" => "PTR",
                    "9" => "PTS",
                    "10" => "Image",
                    "11" => "Composition",
                    "12" => "Brand Since",
                    "13" => "Description",
                    "14" => "Side Effects",
                    "15" => "Indication",
                    "16" => "Is New Product",
                ];

                if ($rowValidator->fails()) {
                    $errorMessages = [];
                    $rowNum = $rowIndex + 1;
                    foreach (
                        $rowValidator->errors()->toArray() as $field => $errors
                    ) {
                        $fieldName = isset($customAttributes[$field])
                        ? $customAttributes[$field]
                        : "Field " . $field;
                        foreach ($errors as $error) {
                            $errorMessages[] = $fieldName . ": " . $error . "Row : " . $rowNum;
                        }
                    }
                    $request
                        ->session()
                        ->flash("alert-error", implode(",", $errorMessages));
                    return redirect()->route("admin.our_product.list");
                }

                $divisionName = trim($row[2]);

                $division = OurDivision::whereRaw(
                    "LOWER(title) = ?",
                    Str::lower($divisionName)
                )->first();

                if (!$division) {
                    $request
                        ->session()
                        ->flash(
                            "alert-error",
                            $divisionName . " Division not found Row:" . ($rowIndex + 1)
                        );
                    return redirect()->route("admin.our_product.list");
                }

                $divisionId = $division->id;

                $categoryName = trim($row[1]);

                $category = OurProductsCategory::whereRaw(
                    "LOWER(category_name) = ?",
                    Str::lower($categoryName)
                )->where('division',$divisionId)
                ->first();

                if (!$category) {
                    $request
                        ->session()
                        ->flash(
                            "alert-error",
                            $categoryName . " Category not found Row:" . ($rowIndex + 1)
                        );
                    return redirect()->route("admin.our_product.list");
                }
                $categoryId = $category->id;

                $path = 'public/uploads/OurProductImage/product-image/';
                $oldImagePath = "";
                $singleImg = trim($row[10]);
                $oldImgName = trim($row[10]);

                if (in_array($singleImg, $seenImages)) {
                    $repeatingImages[] = $singleImg;
                } else {
                    $seenImages[] = $singleImg;
                }

                if (isset($singleImg) && !empty($singleImg)) {
                    $oldImagePath = $path . trim($singleImg);
                    $cleanedFileName = preg_replace('/[^a-zA-Z0-9-.]/', '', trim($singleImg));
                    $newImgName = time() . rand(100, 1000) . '' . $cleanedFileName;
                }
                if (!Storage::exists($oldImagePath)) {
                    $request
                        ->session()
                        ->flash(
                            "alert-error",
                            "Row Number: " . ($rowIndex + 1) . ", The image name doesn't match",
                        );
                    return redirect()->route("admin.our_product.list");
                }

                $productData[] = [
                    "index" => trim($row[0]),
                    "category" => $categoryId,
                    "division_id" => $divisionId,
                    "product_label" => trim($row[3]),
                    "product_title" => trim($row[4]),
                    "packing_type" => trim($row[5]),
                    "packing_size" => trim($row[6]),
                    "mrp" => trim($row[7]),
                    "ptr" => trim($row[8]),
                    "pts" => trim($row[9]),
                    "composition" => trim($row[11]),
                    "label_2" => trim($row[12]),
                    "product_description" => trim($row[13]),
                    "product_side_effect" => trim($row[14]),
                    "product_indication" => trim($row[15]),
                    "is_new_product" => trim($row[16]),
                    'image' => $newImgName,
                    'old_product_image' => $oldImgName,
                ];
            }
        }

        if (!empty($repeatingImages)) {
            $request
                ->session()
                ->flash(
                    "alert-error",
                    "Repeating images: " . implode(', ', array_unique($repeatingImages)) . " All images have unique name. ",
                );
            return redirect()->route("admin.our_product.list");
        }

        if (!empty($errors)) {
            $request->session()->flash("alert-error", $errors);
        }
        foreach ($productData as $item) {
            $key = $item['index'] . $item['product_title'];
            if (!isset($result[$key])) {
                $result[$key] = $item;
            }
        }
        $result = array_values($result);
        if (!empty($result)) {
            DB::transaction(function () use ($result) {
                foreach ($result as $res) {

                    $currentPath = 'public/uploads/OurProductImage/product-image/' . $res['old_product_image'];
                    $newPath = 'public/uploads/OurProductImage/image/' . $res['image'];
                    if (Storage::exists($currentPath)) {
                        Storage::move($currentPath, $newPath);
                    }
                    //  else {
                    //     FacadesRequest::session()->flash("alert-error", "Image not found.");
                    //     return redirect()->route("admin.our_product.list");
                    // }

                    $recordImg = ProductImage::where('image', $res['old_product_image'])->first();
                    if (isset($recordImg) && $recordImg) {
                        if (isset($recordImg->image) && $recordImg->image) {
                            $path = 'public/uploads/OurProductImage/product-image/' . $recordImg->image;
                            if (Storage::exists($path)) {
                                Storage::disk('public')->delete('uploads/OurProductImage/product-image/' . $recordImg->image);
                            }
                        }
                        $recordImg->delete();
                    }

                    $DataProduct = [
                        'category' => $res['category'],
                        'division_id' => $res['division_id'],
                        'product_title' => $res['product_title'],
                        'product_label' => $res['product_label'],
                        'image' => $res['image'],
                        'mrp' => $res['mrp'],
                        'ptr' => $res['ptr'],
                        'pts' => $res['pts'],
                        'packing_type' => $res['packing_type'],
                        'packing_size' => $res['packing_size'],
                        'composition' => $res['composition'],
                        'label_2' => $res['label_2'],
                        'product_description' => $res['product_description'],
                        'product_side_effect' => $res['product_side_effect'],
                        'product_indication' => $res['product_indication'],
                        'is_new_product' => $res['is_new_product'],
                        'status' => 1,
                    ];
                    OurProductImages::create($DataProduct);
                }
            });
            $request->session()->flash("alert-success", "Excel imported successfully");
        } else {
            $request->session()->flash("alert-error", "No Excel file found");
        }
        return redirect()->route("admin.our_product.list");
    }

    public function categoryid(Request $request)
    {
        $categories = OurProductsCategory::where('status', '1')->where('division', $request->division_id)->get();
        return response()->json($categories);
    }
    
}
