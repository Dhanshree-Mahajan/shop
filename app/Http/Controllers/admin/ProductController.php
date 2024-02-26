<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\TempImage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Image;

class ProductController extends Controller
{

    public function index(Request $request){

        $products= Product::latest('id')->paginate(10);
       // $products= Product::latest('id')->with('product_images')->paginate();
       
        if(!empty($request->get('keyword'))) {
            $products = $products->where('title','like','%'.$request->get('keyword').'%');
        }

   // $products= $products->paginate();

    $data['products'] = $products;
     return view('admin.products.list',$data);
    } // end method


    public function create(){

        $data =[];
        $categories = Category::orderBy('name','ASC')->get();
        $data['categories']=$categories;

        return view('admin.products.create',$data);
    } //end method


    public function store(Request $request){

        // dd($request->image_array);
        // exit();
        $rules = [
                 'title' =>'required',
                 'slug' =>'required|unique:products',
                 'sku' =>'required',
                 'price' =>'required|numeric',
                 'track_qty' =>'required|in:Yes,No',
                 'category' =>'required|numeric',
                 'is_featured' =>'required|in:Yes,No',
                 
               ];

               if(!empty($request->track_qty) &&  $request->track_qty =='Yes') {
                  $rules['qty']='required|numeric';
                }

               $validator = Validator::make($request->all(), $rules);

               if($validator->passes()) {
               
                $product = new Product;

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
               $product->save();

               //save gallery pics
               if(!empty($request->image_array)){
                foreach ($request->image_array as $temp_image_id){
                   
                   $tempImageInfo = TempImage::find($temp_image_id);
                   $extArray = explode('.',$tempImageInfo->name); 
                   $ext = last($extArray); 
                   
                    $productImage = new ProductImage();
                    $productImage->product_id=$product->id;
                    $productImage->image = 'NULL';
                    $productImage->save();

                    $imageName = $product->id.'-'.$productImage->id.'-'.time().'.'.$ext;
                    $productImage->image = $imageName;
                    $productImage->save();

                    //generate product thumbnail

                    //Large images
                    $sourcePath = public_path().'/temp/'.$tempImageInfo->name;
                    $destPath = public_path().'/uploads/product/large/'.$imageName;
                    $image = Image::make($sourcePath);
                    $image->resize(1400, null,  function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $image->save($destPath);

                    //small Images

                    $destPath = public_path().'/uploads/product/small/'.$imageName;
                    $image = Image::make($sourcePath);
                    $image->fit(300,300);
                    $image->save($destPath);
                }
               }

               $request->session()->flash('success','Product has been created successfully');

               return response()->json([
                'status' => true,
               'message' => 'product added successfully'
           ]);

            } else {
                         return response()->json([
                            'status' => false,
                           'errors'=> $validator->errors()
                       ]);
                    }

    } //end method


    public function edit(Request $request,$id){
      //dd($request->title);
        $product = Product::find($id);
        
           if(empty($product)){
           
                return redirect()->route('products.index')->with('error','product not found');
           }

        // fetch product images
     // $productImages = ProductImage::where('product_id',$product->id)->get();

        $data =[];
        $data['product'] = $product;
        $categories = Category::orderBy('name','ASC')->get();
        $data['categories']=$categories;
       // $data['productImages']=$productImages;

        return view('admin.products.edit', $data);
  
      } //end method


      public function update(Request $request,$id){

        $product = Product::find($id);

        $rules = [
            'title' =>'required',
            'slug' =>'required|unique:products,slug,'.$product->id.',id',
            'sku' =>'required|unique:products,sku,'.$product->id.',id',
            'price' =>'required|numeric',
            'track_qty' =>'required|in:Yes,No',
            'category' =>'required|numeric',
            'is_featured' =>'required|in:Yes,No',
            
          ];

          if(!empty($request->track_qty) &&  $request->track_qty =='Yes') {
             $rules['qty']='required|numeric';
           }

          $validator = Validator::make($request->all(), $rules);

          if($validator->passes()) {
          
          

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
          $product->save();

          //save gallery pics
          

          $request->session()->flash('success','Product has been updated successfully');

          return response()->json([
           'status' => true,
          'message' => 'product updated successfully'
      ]);

       } else {
                    return response()->json([
                       'status' => false,
                      'errors'=> $validator->errors()
                  ]);
               }
      }


  public function destroy($id, Request $request){
     $product = Product::find($id);

     if(empty($product)){
      $request->session()->flash( 'error', "Product not found" );
      return response()->json([
        'status' => false,
        'notFound' => true
      ]);

      
  }

  // $productImages = ProductImage::where('product_id',$id)->get();

  // if (!empty($productImages)) {
  //   foreach  ($productImages as $productImage) {
  //     File::delete(public_path('uploads/product/large/'.$productImage->image));
  //     File::delete(public_path('uploads/product/small/'.$productImage->image));

  //   }

  //   ProductImage::where('product_id',$id)->delete();
    
  // }
  $product->delete();

 $request->session()->flash( 'success', "Product has been deleted" );
    return response()->json([
      'status' => true,
      'message' => 'product deleted successfully'
    ]);

    

  }


}