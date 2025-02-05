<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::redirect('/', '/admin');

Route::get('preview-invoice/{id}', [InvoiceController::class, 'preview'])->name('preview-invoice');
Route::get('download-invoice/{id}', [InvoiceController::class, 'download'])->name('download-invoice');
Route::get('make-message/{id}', [InvoiceController::class, 'makeMessage'])->name('make-message');

Route::get('send-message/{id}', [MessageController::class, 'sendMessage'])->name('send-message');
