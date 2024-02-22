<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
  public  function index() {
    // $products= Product::latest('id')->paginate();
    $products= Product::latest();
    $data['products'] = $products;
     return view('admin.products.list',$data);
  } // end method

     public  function create(){

         $data =[];
         $categories = Category::orderBy('name','ASC')->get();
         $data['categories']=$categories;

         return view('admin.products.create',$data);
     } // end method

       


    // public function store(Request $request){
      
    //   $rules = [
    //     'title' =>'required',
    //     'slug' =>'required|unique:products',
    //     'price' =>'required|numeric',
    //     'sku' =>'required',
    //     'track_qty' =>'required|in:Yes,No',
    //     'category' =>'required|numeric',
    //     'is_featured' =>'required|in:Yes,No',
    //   ];
      
    //   if(!empty($request->track_qty) &&  $request->track_qty =='Yes') {
    //     $rules['qty']='required|numeric';
    //   }
    //    $validator = Validator::make($request->all(), $rules);

    //     if($validator->passes()) {
    //       $product = new Product;

    //       $product->title = $request->title;
    //       $product->slug = $request->slug;
    //       $product->description = $request->description;
    //       $product->price = $request->price;
    //       $product->compare_price = $request->compare_price;
    //       $product->sku = $request->sku;
    //       $product->barcode = $request->barcode;
    //       $product->track_qty = $request->track_qty;
    //       $product->qty = $request->qty;
    //       $product->status = $request->status;
    //       $product->category_id= $request->category;
    //       $product->is_featured = $request->is_featured;
    //       $product->save();

    //       $request->session()->flash('success','Product has been created successfully');
    //       return response()->json([
    //         'status' => true,
    //         'message' => "Product added successfully" 
    //      ]);

    //     } else {
    //         return response()->json([
    //            'status' => false,
    //            'errors'=> $validator->errors()
    //         ]);
    //     }

    // }  // end method

    public function store(Request $request){
  
      // Validation rules for incoming request data
      $rules = [
        'title' => 'required',
        'slug' => 'required|unique:products',
        'price' => 'required|numeric',
        'sku' => 'required',
        'track_qty' => 'required|in:Yes,No',
        'category' => 'required',
        'is_featured' => 'required|in:Yes,No',
      ];
      
      // Conditionally adding a validation rule based on track_qty field
      if ($request->track_qty == 'Yes') {
        $rules['qty'] = 'required|numeric';
      }
    
      // Validation using Laravel's Validator
      $validator = Validator::make($request->all(), $rules);
    
      // Checking if validation passes
      if ($validator->passes()) {
        // Creating a new Product instance
               $product = new Product();
    
               $product->title = $request->title;
               $product->slug = $request->slug;
               $product->description = $request->description;
               $product->price = $request->price;
               $product->compare_price = $request->compare_price;
               $product->sku = $request->sku;
              $product->barcode = $request->barcode;
              $product->track_qty = $request->track_qty;
               $product->qty = $request->qty;
              $product->status = $request->status;
               $product->category_id= $request->category;
               $product->is_featured = $request->is_featured;
       
               // Populating the product instance with request data
        $product->fill($request->except('_token', 'track_qty'));
    
        // Saving the product to the database
        $product->save();
    
        // Flashing success message to session
        $request->session()->flash('success', 'Product has been created successfully');
    
        // Returning JSON response for success
        return response()->json([
          'status' => true,
          'message' => 'Product added successfully' 
        ]);
      } else {
        // Returning JSON response for validation errors
        return response()->json([
           'status' => false,
           'errors'=> $validator->errors()
        ]);
      }
    }
    
}