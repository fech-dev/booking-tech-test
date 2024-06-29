<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

Route::apiResource('authors', AuthorController::class);
Route::apiResource('books', BookController::class);
