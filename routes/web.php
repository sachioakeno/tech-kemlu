<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WisatawanController; 

Route::get('/', [WisatawanController::class, 'index'])->name('wisatawan.index');

Route::put('/wisatawan/{id}', [WisatawanController::class, 'update'])->name('wisatawan.update');
Route::delete('/wisatawan/{id}', [WisatawanController::class, 'destroy'])->name('wisatawan.destroy');