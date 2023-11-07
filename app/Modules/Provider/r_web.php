<?php
use Illuminate\Support\Facades\Route;

use App\Modules\Provider\Main;
use App\Modules\Provider\B1Main;
use App\Modules\Provider\B2Main;

$MAIN_URI = "provider";
$CLASS = Main::class;
Route::get('/'.$MAIN_URI, [$CLASS, 'index']);

$MAIN_URI = "provider/b1";
$CLASS = B1Main::class;
Route::get('/'.$MAIN_URI."/index", [$CLASS, 'index']);
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

Route::get('/'.$MAIN_URI."/filter", [$CLASS, 'filter']);

$MAIN_URI = "provider/b2";
$CLASS = B2Main::class;
Route::get('/'.$MAIN_URI."/index", [$CLASS, 'index']);
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

Route::get('/'.$MAIN_URI."/filter", [$CLASS, 'filter']);
Route::get('/'.$MAIN_URI."/laboratoryCombo", [$CLASS, 'laboratoryCombo']);


$MAIN_URI = "provider";
$MAIN_URI1 = "provider/b1";
$MAIN_URI2 = "provider/b2";
$CLASS = Main::class;
$CLASS1 = B1Main::class;
$CLASS2 = B2Main::class;

Route::get('/'.$MAIN_URI1."/readProviderTypeCombo", [$CLASS, 'readProviderTypeCombo']);
Route::get('/'.$MAIN_URI2."/readProviderTypeCombo", [$CLASS, 'readProviderTypeCombo']);