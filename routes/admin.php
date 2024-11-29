<?php

use App\Http\Controllers\Admin\ActivitylogController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdvancePaymentController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\BreadController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\CommonController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\SalaryController;
use App\Http\Controllers\Admin\WagesController;

use Illuminate\Support\Facades\Route;

Route::middleware('admin.guest')->group(function() {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login.form');
    Route::post('login', [LoginController::class, 'login'])->name('login.post');
 

    Route::get('password/reset', [LoginController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [LoginController::class, 'sendResetLinkEmail']);

    Route::get('password/reset/{token}', [LoginController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [LoginController::class, 'reset'])->name('password.request.sore');

    Route::get('new-password/{id}', [LoginController::class, 'newPasswordForm'])->name('password.newPassword');
    Route::post('password/set-password/{id}', [LoginController::class, 'sepPassword'])->name('password.setPassword');

   
});


Route::middleware('admin.auth')->group(function() {


    Route::get('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index')->middleware('can:browse_dashboard');
    Route::post('dashboard', [DashboardController::class, 'filter'])->name('dashboard.filter')->middleware('can:browse_dashboard');

    

     //Common
     Route::controller(CommonController::class)->group(function(){
        Route::get('common/client/list', 'clients')->name('clients.list');

    });

    Route::controller(BreadController::class)->group(function(){
        Route::get('bread', 'index')->name('bread.index')->middleware('can:browse_bread');
        Route::get('bread/create', 'create')->name('bread.create')->middleware('can:add_bread');
        Route::get('bread/{slug}/edit', 'edit')->name('bread.edit')->middleware('can:edit_bread');
        Route::put('bread/{bread}/update', 'update')->name('bread.update')->middleware('can:edit_bread');
        Route::delete('bread/{slug}/delete', 'destroy')->name('bread.destroy')->middleware('can:delete_bread');
        Route::post('bread', 'store')->name('bread.store')->middleware('can:add_bread');
    });


    Route::controller(RoleController::class)->group(function(){
        Route::get('role', 'index')->name('role.index')->middleware('can:browse_role');
        Route::get('role/create', 'create')->name('role.create')->middleware('can:add_role');
        Route::get('role/{role}/edit', 'edit')->name('role.edit')->middleware('can:edit_role');
        Route::post('role', 'store')->name('role.store')->middleware('can:add_role');
        Route::put('role/{role}', 'update')->name('role.update')->middleware('can:edit_role');
        Route::delete('role/{slug}/delete', 'destroy')->name('role.destroy')->middleware('can:delete_role');
    });


    Route::controller(MenuController::class)->group(function(){
        Route::get('menu', 'index')->name('menu.index')->middleware('can:browse_menu');
        Route::get('menu/create', 'create')->name('menu.create')->middleware('can:add_menu');
        Route::get('menu/{menu}/edit', 'edit')->name('menu.edit')->middleware('can:edit_menu');
        Route::post('menu', 'store')->name('menu.store')->middleware('can:add_menu');
        Route::put('menu/{menu}', 'update')->name('menu.update')->middleware('can:edit_menu');
        Route::delete('menu/{menu}', 'destroy')->name('menu.destroy')->middleware('can:delete_menu');
    });


     //Admin
    Route::controller(AdminController::class)->group(function(){
        Route::match(['get','patch'],'admin', 'index')->name('admin.index')->middleware('can:browse_admin');
        Route::get('admin/create', 'create')->name('admin.create')->middleware('can:add_admin');
        Route::get('admin/{admin}', 'show')->name('admin.show')->middleware('can:read_admin');
        Route::get('admin/{admin}/edit', 'edit')->name('admin.edit')->middleware('can:edit_admin');
        Route::post('admin', 'store')->name('admin.store')->middleware('can:add_admin');
        Route::put('admin/{admin}', 'update')->name('admin.update')->middleware('can:edit_admin');
        Route::delete('admin/{admin}/delete', 'destroy')->name('admin.destroy')->middleware('can:delete_admin');

        Route::get('profile', 'profile')->name('profile');
        Route::put('profile/update', 'profileUpdate')->name('profile.update');
        Route::put('profile/photo/update/{admin}', 'profilePhotoUpdate')->name('profile.photo.update');
        Route::put('profile/cover/photo/update/{admin}', 'profileCoverPhotoUpdate')->name('profile.cover.photo.update');

        Route::get('change-password/{admin}', 'changePassword')->name('change-password');
        Route::put('update-password/{admin}', 'updatePassword')->name('update-password');

    });

     //Site Setting
    Route::controller(SiteSettingController::class)->group(function(){
        Route::get('get-all-country', 'getAllCountry')->name('site-setting.country')->middleware('can:browse_site_setting');
        Route::get('site-setting', 'index')->name('site-setting.index')->middleware('can:browse_site_setting');
        Route::post('logo', 'logo')->name('site-setting.logo')->middleware('can:logo_site_setting');
    });


    //media
    Route::controller(MediaController::class)->group(function(){
        Route::match(['get','patch'],'media', 'index')->name('media.index')->middleware('can:browse_media');
        Route::get('media/create', 'create')->name('media.create')->middleware('can:add_media');
        Route::get('media/{media}', 'show')->name('media.show')->middleware('can:read_media');
        Route::get('media/{media}/edit', 'edit')->name('media.edit')->middleware('can:edit_media');
        Route::post('media', 'store')->name('media.store')->middleware('can:add_media');
        Route::put('media/{media}', 'update')->name('media.update')->middleware('can:edit_media');
        Route::delete('media/{media}/delete', 'destroy')->name('media.destroy')->middleware('can:delete_media');
        Route::get('media/get/multiple', 'getAllMediaMultiple')->name('media.get.multiple');
        Route::get('media/get/single', 'getAllMediaSingle')->name('media.get.single');
    });


    //client
    Route::controller(ClientController::class)->group(function(){
        Route::match(['get','patch'],'client', 'index')->name('client.index')->middleware('can:browse_client');
        Route::get('client/create', 'create')->name('client.create')->middleware('can:add_client');
        Route::get('client/{client}', 'show')->name('client.show')->middleware('can:read_client');
        Route::get('client/{client}/edit', 'edit')->name('client.edit')->middleware('can:edit_client');
        Route::post('client', 'store')->name('client.store')->middleware('can:add_client');
        Route::put('client/{client}', 'update')->name('client.update')->middleware('can:edit_client');
        Route::delete('client/{client}/delete', 'destroy')->name('client.destroy')->middleware('can:delete_client');
        Route::put('clients/change-status', 'changeStatus')->name('client.changeStatus')->middleware('can:change_status_client');
    });


    //activity-log
    Route::controller(ActivitylogController::class)->group(function(){
        Route::match(['get','patch'],'activity-log', 'index')->name('activity-log.index')->middleware('can:browse_activity_log');
        Route::get('activity-log/create', 'create')->name('activity-log.create')->middleware('can:add_activity_log');
        Route::get('activity-log/{id}', 'show')->name('activity-log.show')->middleware('can:read_activity_log');
        Route::get('activity-log/{id}/edit', 'edit')->name('activity-log.edit')->middleware('can:edit_activity_log');
        Route::post('activity-log', 'store')->name('activity-log.store')->middleware('can:add_activity_log');
        Route::put('activity-log/{id}', 'update')->name('activity-log.update')->middleware('can:edit_activity_log');
        Route::delete('activity-log/{activity-log}/delete', 'destroy')->name('activity-log.destroy')->middleware('can:delete_activity_log');
    });

     //Company
     Route::controller(CompanyController::class)->group(function(){
        Route::match(['get','patch'],'company', 'index')->name('company.index')->middleware('can:browse_company');
        Route::get('company/create', 'create')->name('company.create')->middleware('can:add_company');
        Route::get('company/import', 'import')->name('company.import')->middleware('can:add_company');
        Route::post('company/import', 'storeimport')->name('company.storeimport')->middleware('can:add_company');
        Route::get('company/{company}', 'show')->name('company.show')->middleware('can:read_company');
        Route::get('company/{company}/edit', 'edit')->name('company.edit')->middleware('can:edit_company');
        Route::post('company', 'store')->name('company.store')->middleware('can:add_company');
        Route::put('company/{company}', 'update')->name('company.update')->middleware('can:edit_company');
        Route::delete('company/{company}/delete', 'destroy')->name('company.destroy')->middleware('can:delete_company');
        Route::put('company/change-status', 'changeStatus')->name('company.changeStatus')->middleware('can:change_status_company');
        Route::post('company/verify', 'verifyInsert')->name('company.verify')->middleware('can:add_company');
    });

    Route::controller(EmployeeController::class)->group(function(){
        Route::match(['get','patch'],'employee', 'index')->name('employee.index')->middleware('can:browse_employee');
        Route::get('employee/create', 'create')->name('employee.create')->middleware('can:add_employee');
        Route::get('employee/{employee}', 'show')->name('employee.show')->middleware('can:read_employee');
        Route::get('employee/{employee}/edit', 'edit')->name('employee.edit')->middleware('can:edit_employee');
        Route::get('employee/{employee}/salary', 'salary')->name('employee.salary')->middleware('can:edit_employee');
        Route::post('employee/{employee}/salary', 'storesalary')->name('employee.salary')->middleware('can:edit_employee');
        Route::post('employee', 'store')->name('employee.store')->middleware('can:add_employee');
        Route::put('employee/{employee}', 'update')->name('employee.update')->middleware('can:edit_employee');
        Route::delete('employee/{employee}/delete', 'destroy')->name('employee.destroy')->middleware('can:delete_employee');
        Route::put('employee/change-status', 'changeStatus')->name('employee.changeStatus')->middleware('can:change_status_employee');
        Route::get('employee/uploaded-data/excell/{employee}',  'showUploadedData')->name('employee.uploaded_data.excell')->middleware('can:read_employee');
        
        Route::get('employee/import/excell', 'import')->name('employee.import.excell')->middleware('can:add_employee');
        Route::post('employee/import', 'storeimport')->name('employee.import')->middleware('can:add_employee');
        Route::post('employee/verify', 'verify')->name('employee.verify')->middleware('can:add_employee');
    });
    //Department 
    Route::controller(DepartmentController::class)->group(function(){
        Route::match(['get','patch'],'department', 'index')->name('department.index')->middleware('can:browse_department');
        Route::get('department/create', 'create')->name('department.create')->middleware('can:add_department');
        Route::get('department/{id}', 'show')->name('department.show')->middleware('can:read_department');
        Route::get('department/{id}/edit', 'edit')->name('department.edit')->middleware('can:edit_department');
        Route::post('department', 'store')->name('department.store')->middleware('can:add_department');
        Route::put('department/{id}', 'update')->name('department.update')->middleware('can:edit_department');
        Route::delete('department/{department}/delete', 'destroy')->name('department.destroy')->middleware('can:delete_department');
    });
    //aatendance
    Route::controller(AttendanceController::class)->group(function(){
        Route::match(['get','patch'],'mark-attendance', 'index')->name('mark-attendance.index')->middleware('can:browse_mark_attendance');
        Route::get('mark-attendance/create', 'create')->name('mark-attendance.create')->middleware('can:add_mark_attendance');
        Route::get('mark-attendance/{id}', 'show')->name('mark-attendance.show')->middleware('can:read_mark_attendance');
        Route::get('mark-attendance/{id}/all', 'all')->name('mark-attendance.all')->middleware('can:edit_mark_attendance');
        Route::get('mark-attendance/{id}/edit', 'edit')->name('mark-attendance.edit')->middleware('can:edit_mark_attendance');
        Route::post('mark-attendance', 'store')->name('mark-attendance.store')->middleware('can:add_mark_attendance');
        Route::put('mark-attendance/update',  'updatestore')
        ->name('mark-attendance.updatestore')
        ->middleware('can:edit_mark_attendance');
    
        Route::put('mark-attendance/{id}', 'update')->name('mark-attendance.update')->middleware('can:edit_mark_attendance');
        Route::delete('mark-attendance/{mark-attendance}/delete', 'destroy')->name('mark-attendance.destroy')->middleware('can:delete_mark_attendance');
   
    });
    Route::controller(WagesController::class)->group(function(){
        Route::match(['get','patch'],'wages', 'index')->name('wages.index')->middleware('can:browse_wages');
        Route::get('wages/create', 'create')->name('wages.create')->middleware('can:add_wages');
        Route::get('wages/{minimum_wages}', 'show')->name('wages.show')->middleware('can:read_wages');
        Route::get('wages/{minimum_wages}/edit', 'edit')->name('wages.edit')->middleware('can:edit_wages');
        Route::post('wages', 'store')->name('wages.store')->middleware('can:add_wages');
        Route::put('wages/{minimum_wages}', 'update')->name('wages.update')->middleware('can:edit_wages');
        Route::delete('wages/{minimum_wages}/delete', 'destroy')->name('wages.destroy')->middleware('can:delete_wages');
        Route::put('wages/change-status', 'changeStatus')->name('wages.changeStatus')->middleware('can:change_status_wages');
    });
    //advance_payment 
    Route::controller(AdvancePaymentController::class)->group(function(){
        Route::match(['get','patch'],'advance-payment', 'index')->name('advance-payment.index')->middleware('can:browse_advance_payment');
        Route::get('advance-payment/create', 'create')->name('advance-payment.create')->middleware('can:add_advance_payment');
        Route::get('advance-payment/{id}', 'show')->name('advance-payment.show')->middleware('can:read_advance_payment');
        Route::get('advance-payment/{id}/all', 'all')->name('advance-payment.all')->middleware('can:edit_advance_payment');
        Route::post('advance-payment', 'store')->name('advance-payment.store')->middleware('can:add_advance_payment');
        Route::put('advance-payment/{id}', 'update')->name('advance-payment.update')->middleware('can:edit_advance_payment');
        Route::delete('advance-payment/{advance-payment}/delete', 'destroy')->name('advance-payment.destroy')->middleware('can:delete_advance_payment');
    });

    //salary
      Route::controller(SalaryController::class)->group(function(){
        Route::match(['get','patch'],'salary-export', 'index')->name('salary-export.index')->middleware('can:browse_salary_export');
        Route::get('salary-export/create', 'create')->name('salary-export.create')->middleware('can:add_salary_export');
        Route::get('salary-export/{id}', 'show')->name('salary-export.show')->middleware('can:read_salary_export');
        Route::get('salary-export/{id}/all', 'all')->name('salary-export.all')->middleware('can:edit_salary_export');
        Route::post('salary-export', 'store')->name('salary-export.store')->middleware('can:add_salary_export');
        Route::put('salary-export/{id}', 'update')->name('salary-export.update')->middleware('can:edit_salary_export');
        Route::delete('salary-export/{salary-export}/delete', 'destroy')->name('salary-export.destroy')->middleware('can:delete_salary_export');
    });
    Route::get('/admin/get-employees', [AdvancePaymentController::class, 'getEmployees'])->name('admin.getEmployees');


});
