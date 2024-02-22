<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\admin\Adminlogincontroller;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\CategoryController;

use App\Http\Controllers\admin\TempImagesController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\Product1Controller;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});



Route::group(['prefix'=>'admin'],function(){
    Route::group(['middleware'=>'admin.guest'],function(){
        Route::get('/login',[Adminlogincontroller::class,'index'])->name('admin.login');
        Route::post('/authenticate',[Adminlogincontroller::class,'authenticate'])->name('admin.authenticate');

    });

    Route::group(['middleware'=>'admin.auth'],function(){

         Route::get('/dashboard',[HomeController::class,'index'])->name('admin.dashboard');
         Route::get('/logout',[HomeController::class,'logout'])->name('admin.logout');


         //category Routes
         Route::get('/categories',[CategoryController::class,'index'])->name('categories.index');
         Route::get('/categories/create',[CategoryController::class,'create'])->name('categories.create');
         Route::post('/categories',[CategoryController::class,'store'])->name('categories.store');
         Route::get('/categories/{category}/edit',[CategoryController::class,'edit'])->name('categories.edit');

         Route::delete('/categories/{category}',[CategoryController::class,'destroy'])->name('categories.delete');


         //Product Routes
         Route::get('/products',[ProductController::class,'index'])->name('products.index');
         Route::get('/products/create',[ProductController::class,'create'])->name('products.create');
         Route::post('/products',[ProductController::class,'store'])->name('product.store');

         
         //product1 routes
         Route::get('/product1/create',[Product1Controller::class,'create'])->name('product1.create');
         Route::post('/product1',[Product1Controller::class,'store'])->name('product1.store');


         //temp-images.create 
         Route::post('/upload-temp-image',[TempImagesController::class,'create'])->name('temp-images.create');


        Route::get('/getSlug',function(Request $request){
             $slug ='';
            if(!empty($request->title)) {
               $slug = Str::slug($request->title);
               
            }
            return response()->json(['status' => true,'slug' => $slug]);
        })->name('getSlug');

    });
});