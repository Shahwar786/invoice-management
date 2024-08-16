<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;

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
    return redirect()->route('login');
});

// Authentication Routes
Auth::routes();

// Your existing routes for invoices and dashboards

Route::get('/invoices', [InvoiceController::class, 'getInvoicesByCurrency']);
Route::get('/api/invoices', [InvoiceController::class, 'fetchInvoicesByFilters']);
Route::get('/api/invoices/currencies', [InvoiceController::class, 'getCurrencies']);

// Define an array of roles and corresponding prefixes
$roles = [
    'Admin' => 'admin',
    'Camp' => 'camp',
    'Sales Supervisor' => 'sales-supervisor',
    'Accounts' => 'accounts',
    'Staff' => 'staff',
    'Kitchen' => 'kitchen',
];

// Dynamically create routes for each role
foreach ($roles as $role => $prefix) {
    Route::group(['middleware' => ['auth', "role:$role"], 'prefix' => $prefix], function () use ($prefix) {
        Route::get('/dashboard', function () use ($prefix) {
            return view("$prefix.dashboard");
        });
        Route::get('/about', function () use ($prefix) {
            return view("$prefix.about");
        });
        Route::get('/contact', function () use ($prefix) {
            return view("$prefix.contact");
        });
    });
}
