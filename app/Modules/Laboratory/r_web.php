<?php
use Illuminate\Support\Facades\Route;

use App\Modules\Laboratory\Main;

$MAIN_URI = "laboratory";
$CLASS = Main::class;
Route::get('/'.$MAIN_URI, [$CLASS, 'index']);
Route::get('/'.$MAIN_URI."/filter", [$CLASS, 'filter']);
Route::get('/'.$MAIN_URI."/read", [$CLASS, 'read']);
Route::get('/'.$MAIN_URI."/add", [$CLASS, 'add']);
Route::post('/'.$MAIN_URI."/create", [$CLASS, 'create']);
Route::get('/'.$MAIN_URI."/edit/{id?}", [$CLASS, 'edit']);
Route::put('/'.$MAIN_URI."/update/{id?}", [$CLASS, 'update']);
Route::delete('/'.$MAIN_URI."/delete/{id?}", [$CLASS, 'delete']);
Route::get('/'.$MAIN_URI."/export", [$CLASS, 'export']);
Route::get('/'.$MAIN_URI."/import", [$CLASS, 'import']);
Route::get('/'.$MAIN_URI."/importTemplate", [$CLASS, 'importTemplate']);
Route::post('/'.$MAIN_URI."/importSave", [$CLASS, 'importSave']);