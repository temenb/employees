<?php

use Illuminate\Support\Facades\Route;

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
    return redirect()->route('employees');
});


Route::get('/employees', [App\Http\Controllers\EmployeeController::class, 'index'])->name('employees');
Route::get('/employees/create', [App\Http\Controllers\EmployeeController::class, 'create'])->name('employees.create');
Route::post('/employees/create', [App\Http\Controllers\EmployeeController::class, 'insert'])->name('employees.create');
Route::post('/employees/delete', [App\Http\Controllers\EmployeeController::class, 'delete'])->name('employees.delete');
Route::get('/employees/edit/{id}', [App\Http\Controllers\EmployeeController::class, 'edit'])->name('employees.edit');
Route::post('/employees/edit/{id}', [App\Http\Controllers\EmployeeController::class, 'patch'])->name('employees.edit');

Route::get('/employees/custom_export', [App\Http\Controllers\EmployeeController::class, 'customExport'])->name('employees.custom_export');
Route::get('/employees/export', [App\Http\Controllers\EmployeeController::class, 'export'])->name('employees.export');
Route::post('/employees/import', [App\Http\Controllers\EmployeeController::class, 'import'])->name('employees.import');
Route::post('/employees/compare', [App\Http\Controllers\EmployeeController::class, 'compare'])->name('employees.compare');

Route::get('/schedules/create/employee/{employeeId}', [App\Http\Controllers\ScheduleController::class, 'create'])->name('schedules.create');
Route::post('/schedules/create/employee/{employeeId}', [App\Http\Controllers\ScheduleController::class, 'insert'])->name('schedules.create');
Route::post('/schedules/delete', [App\Http\Controllers\ScheduleController::class, 'delete'])->name('schedules.delete');
Route::get('/schedules/edit/{ids}', [App\Http\Controllers\ScheduleController::class, 'edit'])->name('schedules.edit');
Route::post('/schedules/edit/{ids}', [App\Http\Controllers\ScheduleController::class, 'patch'])->name('schedules.edit');
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
