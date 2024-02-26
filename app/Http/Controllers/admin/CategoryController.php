<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;
use Image;

class CategoryController extends Controller
{
    
    public function index(Request $request){
        $categories= Category::latest();

        if(!empty($request->get('keyword'))) {
            $categories= $categories->where('name','like','%'.$request->get('keyword').'%');
        }


     $categories= $categories->paginate(10);
    // $data['categories'] = $categories; 
     return view('admin.category.list',compact('categories'));

    } //end method


    public function create(){
       return view('admin.category.create');
       
    } //end method


    public function store(Request $request){
      $validator = Validator::make($request->all(),[
        'name' => 'required',
        'slug' => 'required|unique:categories',
      ]);

      if($validator->passes()){
           $category = new Category();
           $category->name = $request->name;
           $category->slug = $request->slug;
           $category->status = $request->status;
           $category->save();


           // save image here

            if(!empty($request->image_id)){
              $tempImage = TempImage::find($request->image_id);
              $extArray = explode('.',$tempImage->name);
              $ext = last($extArray);

              $newImageName = $category->id.'.'.$ext;
              $sPath = public_path().'/temp'.$tempImage->name;
              $dPath = public_path().'/uploads/category'.$newImageName;
              File::copy($sPath,$dPath);

              // generate thumbnail
              $dPath = public_path().'/uploads/category/thumb/'.$newImageName;
              $img = Image::make($sPath);
              //$img->resize(450, 600);
              $img->fit(450,600,function ($constraint) {
                $constraint->upsize();
                });
              $img->save($dPath);

               $category->image = $newImageName;
               $category->save();

            }

           $request->session()->flash('success','category added successfully');

           return response()->json([
            'status'=>true,
            'message'=> "category added successfully"
          ]);

      } else{
              return response()->json([
                'status'=>false,
                'errors'=> $validator->errors()
              ]);

      }
    } //end method


    public function edit(Request $request,$categoryId){

      $category = Category::find('$categoryId');
      // if (empty($category)) {
        
      //   return redirect()->route('categories.index');

      // }
      return view('admin.category.edit',compact( 'category'));
      


    } //end method


    public function update($categoryId, Request $request){

      $category = Category::find('$categoryId');

       if (empty($category)) {
        
         return response()->json([
          'status' => false,
          'notFound' => true,
          'massage' => 'Category not found'
         ]);

       }

      $validator = Validator::make($request->all(),[
        'name' => 'required',
        'slug' => 'required|unique:categories,slug,'.$category->id.',id',
      ]);

      if($validator->passes()){
           
           $category->name = $request->name;
           $category->slug = $request->slug;
           $category->status = $request->status;
           $category->save();

           $oldImage = $category->image;

           // save image here

            if(!empty($request->image_id)){
              $tempImage = TempImage::find($request->image_id);
              $extArray = explode('.',$tempImage->name);
              $ext = last($extArray);

              $newImageName = $category->id.'-'.time().'.'.$ext;
              $sPath = public_path().'/temp'.$tempImage->name;
              $dPath = public_path().'/uploads/category'.$newImageName;
              File::copy($sPath,$dPath);

              // generate thumbnail
              $dPath = public_path().'/uploads/category/thumb/'.$newImageName;
              $img = Image::make($sPath);
              // $img->resize(450, 600);
              $img->fit(450,600,function ($constraint) {
                $constraint->upsize();
                });
              $img->save($dPath);

               $category->image = $newImageName;
               $category->save();

               // Delete old images
               File::delete(public_path().'/uploads/category/thumb/'.$oldImage);
               File::delete(public_path().'/uploads/category/'.$oldImage);
            }

           $request->session()->flash('success','category updated successfully');

           return response()->json([
            'status'=>true,
            'message'=> "category updated successfully"
          ]);

      } else{
              return response()->json([
                'status'=>false,
                'errors'=> $validator->errors()
              ]);

      }

    } //end method


    public function destroy($categoryId, Request $request){
      $category = Category::find($categoryId);

      File::delete(public_path().'/uploads/category/thumb/'.$category->image);
      File::delete(public_path().'/uploads/category/'.$category->image);
      $category->delete();

      $request->session()->flash("success", 'category has been deleted');

      return response()->json([
        'status' =>true,
        'message' =>'category deleted successfully'
      ]);
    } //end method

}
