<?php

use App\Http\Controllers\AuthorController;
use Illuminate\Support\Facades\Route;

Route::apiResource('authors', AuthorController::class);
