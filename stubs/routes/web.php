<?php

use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::get('/StubModuleName', function () {
        return view('StubModuleName::index');
    })->name('StubModuleName.index');
});
