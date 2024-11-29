<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CommonController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\AdvancePaymentController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/get-districts/{stateId}', [CommonController::class, 'getDistricts']);
Route::get('/get-cities/{districtId}', [CommonController::class, 'getCities']);
Route::get('/departments-by-company', [DepartmentController::class, 'getDepartmentsByCompany'])->name('admin.get-departments-by-company');
Route::get('/admin/get-employees', [AdvancePaymentController::class, 'getEmployees'])->name('admin.getEmployees');
Route::get('/admin/get', [AdvancePaymentController::class, 'getWorkingDays'])->name('admin.getWorkingDays');

require __DIR__.'/auth.php';
