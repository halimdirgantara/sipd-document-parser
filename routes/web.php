<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('layouts.base');
// });

Route::get('/', App\Livewire\Document\Upload::class)->name('document.upload');

Route::get('/login', function () {
    return 'login';
})->name('login');
