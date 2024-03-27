<?php

use Illuminate\Support\Facades\Route;

Route::get('{any?}', function() {
    return view('application',[
        "apiUrl" => env("VUE_BASE_URL","http://127.0.0.1:8000/"),
    ]);
})->where('any', '.*');
