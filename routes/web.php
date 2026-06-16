<?php

use App\Http\Controllers\FormBuilderController;
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

Route::get('/', [FormBuilderController::class, 'index'])->name('home');
Route::post('/schemas/export', [FormBuilderController::class, 'export'])->name('schemas.export');
Route::get('/schemas', [FormBuilderController::class, 'schemas'])->name('schemas.index');
Route::get('/schemas/{filename}/download', [FormBuilderController::class, 'download'])->name('schemas.download');
Route::get('/schemas/{filename}/edit', [FormBuilderController::class, 'edit'])->name('schemas.edit');
Route::delete('/schemas/{filename}', [FormBuilderController::class, 'destroy'])->name('schemas.destroy');
