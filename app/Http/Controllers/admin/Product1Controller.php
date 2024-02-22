<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Product1Controller extends Controller
{
    public  function create(){

         $data =[];

         $categories = Category::orderBy('name','ASC')->get();
         $data['categories']=$categories;

        return view('admin.product1.create',$data);
    } // end method


    public function store(Request $request){
    
        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products',
            'price' => 'required|numeric',
            'sku' => 'required',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required',
            'is_featured' => 'required|in:Yes,No',
          ];

          if (!empty($request->track_qty) && $request->track_qty == 'Yes') {
            $rules['qty'] = 'required|numeric';
          }

          $validator = Validator::make($request->all(), $rules);

          if ($validator->passes()) {

            $product1 = new Product1();
            $product1->title  = $request->title;
            $product1->slug   = $request->slug;
            $product1->description  = $request->description;
            $product1->price  = $request->price;
            $product1->compare_price  = $request->compare_price;
            $product1->sku  = $request->sku;
            $product1->barcode  = $request->barcode;
            $product1->track_qty  = $request->track_qty;
            $product1->qty  = $request->qty;
            $product1->status  = $request->status;
            $product1->category_id  = $request->category_id;
            $product1->is_featured  = $request->is_featured;
            $product1->save();

            return response()->json([
                'status' => true,
                'message' => 'product created successfully'
             ]);
             
             $request->session()->flash('success', 'Product has been created successfully');

          } else {
            return response()->json([
                'status' => false,
                'errors'=> $validator->errors()
             ]);
          }
    
    }

}