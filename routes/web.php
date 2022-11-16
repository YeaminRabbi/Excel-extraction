<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;

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

Auth::routes(['verify' => true]);
Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    return redirect()->route('admin_dashboard');
});

Route::post('/user-login', [LoginController::class, 'LOGIN'])->name('LOGIN');

#~~~~~~~~~~~~ all admin panel routes ~~~~~~~~~~~~~~

Route::group(['prefix' => 'admin','middleware' => ['auth', 'role:admin']], function() {
    
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin_dashboard');
    


    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'profile_edit'])->name('profile_edit');
    Route::post('/profile/update', [ProfileController::class, 'profile_update'])->name('profile_update');

  

});

Route::get('/school', [SchoolController::class, 'index'])->name('school');
Route::post('/school-upload', [SchoolController::class, 'upload'])->name('school-upload');

