<?php

use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/invoices', [InvoiceController::class, 'getInvoicesByCurrency']);


Route::prefix('admin')->middleware('role:Admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    });
    Route::get('/about', function () {
        return view('admin.about');
    });
    Route::get('/contact-us', function () {
        return view('admin.contact');
    });
});

Route::prefix('camp')->middleware('role:Camp')->group(function () {
    Route::get('/dashboard', function () {
        return view('camp.dashboard');
    });
    Route::get('/about', function () {
        return view('camp.about');
    });
    Route::get('/contact-us', function () {
        return view('camp.contact');
    });
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
