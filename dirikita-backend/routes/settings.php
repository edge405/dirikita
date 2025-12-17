<?php

use Illuminate\Support\Facades\Route;

// Load User module routes
Route::middleware('web')->group(function () {
    require __DIR__.'/../app/Modules/User/Routes/api.php';
});
