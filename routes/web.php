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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [App\Http\Controllers\XmlController::class,'show']);
Route::post('/upload_file', [App\Http\Controllers\XmlController::class,'upload_xml'])->name('upload.xml');
Route::post('/import_xml', [App\Http\Controllers\XmlController::class,'import_xml'])->name('import.xml');
Route::post('/delete_xml', [App\Http\Controllers\XmlController::class,'delete_xml'])->name('xml.destroy');

