<?php

use App\Http\Controllers\StoredFileController;
use Illuminate\Support\Facades\Route;

Route::apiResource('stored-files', StoredFileController::class);
