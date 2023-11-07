<?php
use Illuminate\Support\Facades\Route;

use App\Modules\Image\Main;
use App\Modules\Image\B1Main;
use App\Modules\Image\B2Main;

$MAIN_URI = "image";
$CLASS = Main::class;
Route::get('/'.$MAIN_URI, [$CLASS, 'index']);

$MAIN_URI = "image/b1";
$CLASS = B1Main::class;
Route::get('/'.$MAIN_URI."/index", [$CLASS, 'index']);
Route::get('/'.$MAIN_URI."/read", [$CLASS, 'read']);
Route::get('/'.$MAIN_URI."/add", [$CLASS, 'add']);
Route::post('/'.$MAIN_URI."/create", [$CLASS, 'create']);
// Route::get('/'.$MAIN_URI."/edit/{id?}", [$CLASS, 'edit']);
// Route::put('/'.$MAIN_URI."/update/{id?}", [$CLASS, 'update']);
Route::get('/'.$MAIN_URI."/showImage/{code?}", [$CLASS, 'showImage']);
Route::delete('/'.$MAIN_URI."/delete/{code?}", [$CLASS, 'delete']);

Route::get('/'.$MAIN_URI."/filter", [$CLASS, 'filter']);